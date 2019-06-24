<?php

namespace YP;

class Form {

  public function buildStatusField( $current = [] ) {

    $terms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_status' );

    $labels = [
      'label' => __( 'Status', 'yp-ticketing-system' ),
      'placeholder' => __( 'Select a status', 'yp-ticketing-system' ),
      'id' => 'ticket_status',
    ];

    $markup = $this->buildSelectField( $terms, $labels, $current );
    return $markup;

  }

  public function buildPriorityField( $current = [] ) {

    $terms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_priority' );

    $labels = [
      'label' => __( 'Priority', 'yp-ticketing-system' ),
      'placeholder' => __( 'Select a priority', 'yp-ticketing-system' ),
      'id' => 'ticket_priority',
    ];

    $markup = $this->buildSelectField( $terms, $labels, $current );
    return $markup;

  }

  public function buildServiceField( $current = [] ) {

    $terms = CustomTaxonomies::getTermsFromTaxonomy( 'ticket_service' );

    $labels = [
      'label' => __( 'Service', 'yp-ticketing-system' ),
      'placeholder' => __( 'Select a service', 'yp-ticketing-system' ),
      'id' => 'ticket_service',
    ];

    $markup = $this->buildSelectField( $terms, $labels, $current );
    return $markup;

  }

  public function buildSelectField( $terms, $labels, $current = [] ) {

    $markup = '<label for="' . $labels[ 'id' ] . '">' . $labels[ 'label' ] .'</label>
              <div class="select-field">
                <select name="' . $labels[ 'id' ] . '" id="' . $labels[ 'id' ] . '" required>
                  <option value="" disabled selected>' . $labels[ 'placeholder' ] . '</option>';

                  foreach ( $terms as $item ):
                    if ( !empty( $current ) && $current->slug == $item->slug ) {
                      $markup .= '<option value="' . $item->slug . '" selected>' . $item->name .'</option>';
                    } else {
                      $markup .= '<option value="' . $item->slug . '">' . $item->name .'</option>';
                    }
                  endforeach;

                $markup .= '</select>
              </div>';

    return $markup;

  }

  public function buildUploadField() {

    $markup = '<div class="col-12 form-field col-md-6">
              <label for="attachment">' . __( 'Upload file', 'yp-ticketing-system' ) .'</label>
              <div class="upload-field">
                <input type="attachment" name="attachment" />
                <button class="js-file-upload">Upload file</button>
                <span class="js-file-name"></span>
              </div><!-- .upload-field -->
            </div>';

    return $markup;

  }

  public function buildBuildingField() {

    $buildings = \YP\Users::getMemberBuildings();

    $markup = '<label for="building-name">' . __( 'Building', 'yp-ticketing-system' ) . '</label>';

    if ( count( $buildings ) > 1 ) {

      $markup .= '<div class="select-field">';
        $markup .= '<select name="ticket_building">';
          foreach ( $buildings as $building ) {
            $markup .= '<option value="' . $building . '">' . get_the_title( $building ) . '</option>';
          }
        $markup .= '</select>';
      $markup .= '</div>';

    } else {

      $markup .= '<div class="disabled-field">
              <i class="fas fa-pencil-alt banned"></i>';
      $markup .= '<input disabled type="text" name="building-name" value="' . get_the_title( $buildings[0] ) . '" />';
      $markup .= '</div>';

    }

    return $markup;
  }

  public static function buildUserField( $currentUser ) {

    $usermeta = get_userdata( $currentUser->ID );
    $markup = '<label for="user-name">' . __( 'Name', 'yp-ticketing-system' ) . '</label>';
    $markup .= '<div class="disabled-field">';

      $markup .= '<i class="fas fa-pencil-alt banned"></i>';

      $name = $usermeta->display_name;

      if ( empty( $name ) ) {
        $name = ucfirst( $usermeta->user_nicename );
      }

      $markup .= '<input disabled type="text" name="user_name" value="' . $name . '" />
      <input disabled type="hidden" name="user_id" value="' . $usermeta->ID . '" />
    </div>';

    return $markup;
  }

  public static function buildRoleField( $currentUser ) {

    global $wp_roles;
    $usermeta = get_userdata( $currentUser->ID );

    $markup = '<label for="user-role">' . __( 'Role', 'yp-ticketing-system' ) . '</label>';

    $markup .= '<div class="disabled-field">
      <i class="fas fa-pencil-alt banned"></i>';

        $roleSlug = $usermeta->roles[0];
        $role = $wp_roles->role_names[ $roleSlug ];

      $markup .= '<input disabled type="text" name="user-role" value="' . $role . '" />
    </div>';

    return $markup;

  }

}
