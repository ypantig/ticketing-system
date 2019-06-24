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

  /**
   * get all the buildings the specified user is a member of
   */
  public static function getMemberBuildings( $userID = '', $type = '' ) {

    if ( $userID == '' ) {
      $userID = get_current_user_id();
    }

    $buildings = get_field( 'building', 'user_' . $userID );

    if ( $type == 'names' ) {
      $data = [];
      if ( !empty( $buildings ) ) {
        foreach ( $buildings as $item ) {
          $data[] = get_the_title( $item );
        }
      }
    } else {
      $data = $buildings;
    }

    return $data;

  }

  public static function getMemberMetaQuery( $key = 'building' ) {

    $memberBuilding = \YP\Users::getMemberBuildings();
    $metaQuery = [
      'relation' => 'OR',
    ];

    foreach ( $memberBuilding as $building )
    {
      $metaQuery[] = [
        'key' => $key,
        'value' => $building,
        'compare' => 'LIKE',
      ];
    }

    return $metaQuery;
  }
}
