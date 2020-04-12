<?php

namespace plathir\apps\components\permissions;

class AppPermissionsHelper {

    public $appname = '';

    public function createRoles($auth, $roles) {

        foreach ($roles as $role) {
            if (!$auth->getRole($role["name"])) {
                $NewRole = $auth->createRole($role["name"]);
                $NewRole->description = $role["description"];
                if (array_key_exists("rule", $role)) {
                    $NewRule = $role["rule"];
                    if (!$auth->getRule($role["rule"])) {
                        $auth->add($NewRule);
                    }
                    $NewRole->ruleName = $NewRule->name;
                }
                $auth->add($NewRole);
                if (array_key_exists("parent_role", $role)) {
                    if ($tmpRole = $auth->getRole($role["parent_role"])) {
                        $auth->addChild($tmpRole, $NewRole);
                    }
                }

                if (array_key_exists("permissions", $role)) {
                    foreach ($role["permissions"] as $permission) {
                        // Create new Permission

                        if (!$auth->getPermission($permission["name"])) {
                            $NewPermission = $auth->createPermission($permission["name"]);
                            $NewPermission->description = $permission["description"];
                            $auth->add($NewPermission);
                            if (array_key_exists("rule", $permission)) {
                                $NewRule = $permission["rule"];
                                if (!$auth->getRule($NewRule->name)) {
                                    $auth->add($NewRule);
                                }
                                $NewPermission->ruleName = $NewRule->name;
                            }
                        } else {
                            $NewPermission = $auth->getPermission($permission["name"]);
                        }


                        //add Permission to Role
                        $auth->addChild($NewRole, $NewPermission);
                    }
                }
            }
        }
    }

    public function deleteRoles($auth, $roles) {

        foreach ($roles as $role) {
            if ($tmpRole = $auth->getRole($role["name"])) {
                $auth->removeChildren($tmpRole);
                $auth->remove($tmpRole);

                if (array_key_exists("rule", $role)) {
                    $helpRule = $role["rule"];
                    if ($tmpRule = $auth->getRule($helpRule->name)) {
                        $auth->remove($tmpRule);
                    }
                }

                if (array_key_exists("permissions", $role)) {
                    foreach ($role["permissions"] as $permission) {
                        if ($tmpPermission = $auth->getPermission($permission["name"])) {
                            $auth->remove($tmpPermission);
                        }
                        if (array_key_exists("rule", $permission)) {
                            $helpRule = $permission["rule"];
                            if ($tmpRule = $auth->getRule($helpRule->name)) {
                                $auth->remove($tmpRule);
                            }
                        }
                    }
                }
            }
        }
    }

}
