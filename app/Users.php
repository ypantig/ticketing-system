<?php

namespace YP;

class Users {

  public static function adminRoles() {

    $roles = [
      'administrator',
      'atira-admin',
      'um_property-manager',
    ];

    return $roles;
  }

  public static function allowedAdminUser( $userRoles ) {

    $adminRoles = Users::adminRoles();

    foreach ( $adminRoles as $role ) {
      // check if an admin
      if ( in_array( $role, $userRoles ) ) {
        return true;
      }
    }

    return false;

  }

  public static function getAdmins() {

    $roles = Users::adminRoles();
    $args = [
      'role__in' => $roles
    ];

    $users = new \WP_User_Query( $args );
    $users = $users->get_results();

    return $users;

  }
}
