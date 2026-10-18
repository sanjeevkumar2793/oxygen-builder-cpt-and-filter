<?php
/**
 * Plugin Name: HCS Course Sessions
 * Description: Course Sessions CPT + ACF fields. Standalone, update-safe, hardened.
 * Version:     1.0.1
 * Author:      Sanjeev Kumar
 */

// 1) Direct access block
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 2) Guard: agar same function naam kisi aur jagah ho to double-load na ho
if ( ! function_exists( 'hcs_register_course_session_cpt' ) ) {

    define( 'HCS_CS_VERSION', '1.0.1' );

    /**
     * Register Custom Post Type (safe + conflict-free)
     */
    add_action( 'init', 'hcs_register_course_session_cpt', 11 );

    function hcs_register_course_session_cpt() {

        // Pehle se bana ho to touch nahi karte
        if ( post_type_exists( 'course_session' ) ) {
            return;
        }

        // Slug ko strictly sanitize (injection se bacha)
        $slug = sanitize_title( 'sessions' );

        register_post_type(
            'course_session',
            array(
                'labels'          => array(
                    'name'          => __( 'Course Sessions', 'hcs' ),
                    'singular_name' => __( 'Course Session', 'hcs' ),
                    'add_new_item'  => __( 'Add New Session', 'hcs' ),
                    'edit_item'     => __( 'Edit Session', 'hcs' ),
                    'all_items'     => __( 'All Sessions', 'hcs' ),
                    'menu_name'     => __( 'Course Sessions', 'hcs' ),
                ),
                'public'          => true,
                'show_ui'         => true,
                'show_in_menu'    => true,
                'menu_icon'       => 'dashicons-calendar-alt',
                'supports'        => array( 'title' ),
                'has_archive'     => false,
                'rewrite'         => array( 'slug' => $slug, 'with_front' => false ),
                'show_in_rest'    => true,
                'capability_type' => 'post',
                'map_meta_cap'    => true, // proper edit/delete permission checks
            )
        );
    }

    /**
     * Rewrite rules sirf ek baar flush (performance + safety).
     * Har request pe flush karna bad practice hai.
     */
    add_action( 'init', 'hcs_maybe_flush_rewrites', 99 );

    function hcs_maybe_flush_rewrites() {
        if ( get_option( 'hcs_cs_rewrite_version' ) !== HCS_CS_VERSION ) {
            flush_rewrite_rules( false );
            update_option( 'hcs_cs_rewrite_version', HCS_CS_VERSION );
        }
    }

    /**
     * ACF fields via code (guarded + type-validated).
     */
    add_action( 'acf/init', 'hcs_register_acf_fields' );

    function hcs_register_acf_fields() {

        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group(
            array(
                'key'      => 'group_hcs_session_fields',
                'title'    => 'Course Session Fields',
                'fields'   => array(

                    array(
                        'key'     => 'field_hcs_course_name',
                        'label'   => 'Course',
                        'name'    => 'course_name',
                        'type'    => 'select',
                        'choices' => array(
                            'Intermediate First Aid' => 'Intermediate First Aid',
                            'Basic First Aid'        => 'Basic First Aid',
                            'CPR/AED Level C'        => 'CPR/AED Level C',
                            'BLS Provider'           => 'BLS Provider',
                            'ACLS Provider'          => 'ACLS Provider',
                            'PALS Provider'          => 'PALS Provider',
                            'Mask Fit Testing'       => 'Mask Fit Testing',
                            'WHMIS Training'         => 'WHMIS Training',
                        ),
                    ),
                    array(
                        'key'     => 'field_hcs_city',
                        'label'   => 'City',
                        'name'    => 'city',
                        'type'    => 'select',
                        'choices' => array(
                            'North York'  => 'North York',
                            'Mississauga' => 'Mississauga',
                            'Brampton'    => 'Brampton',
                            'Scarborough' => 'Scarborough',
                            'Etobicoke'   => 'Etobicoke',
                        ),
                    ),
                    array(
                        'key'            => 'field_hcs_session_date',
                        'label'          => 'Session Date',
                        'name'           => 'session_date',
                        'type'           => 'date_picker',
                        'return_format'  => 'Y-m-d',   // safe, predictable format
                        'display_format' => 'D, M j, Y',
                    ),
                    array(
                        'key'   => 'field_hcs_start_time',
                        'label' => 'Start Time',
                        'name'  => 'start_time',
                        'type'  => 'text',
                    ),
                    array(
                        'key'   => 'field_hcs_end_time',
                        'label' => 'End Time',
                        'name'  => 'end_time',
                        'type'  => 'text',
                    ),
                    array(
                        'key'     => 'field_hcs_format',
                        'label'   => 'Format',
                        'name'    => 'format',
                        'type'    => 'select',
                        'choices' => array(
                            'In-class' => 'In-class',
                            'Blended'  => 'Blended',
                            'Online'   => 'Online',
                            'On-site'  => 'On-site',
                        ),
                    ),
                    array(
                        'key'     => 'field_hcs_seats_left',
                        'label'   => 'Seats Left',
                        'name'    => 'seats_left',
                        'type'    => 'number',
                        'min'     => 0,
                        'step'    => 1,
                    ),
                    array(
                        'key'   => 'field_hcs_details_url',
                        'label' => 'Details URL',
                        'name'  => 'details_url',
                        'type'  => 'url',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param'    => 'post_type',
                            'operator' => '==',
                            'value'    => 'course_session',
                        ),
                    ),
                ),
            )
        );
    }
}

/** 
 * Front-end filter script (footer me load hota hai)
 */
if ( ! function_exists( 'hcs_course_filter_script' ) ) {

    add_action( 'wp_footer', 'hcs_course_filter_script', 99 );

    function hcs_course_filter_script() {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          var courseSel = document.getElementById('finder-course');
          var citySel   = document.getElementById('finder-city');
          var formatSel = document.getElementById('finder-format');
          var dateInp   = document.getElementById('finder-date');

          var list = document.querySelector('.hcs-list') ||
                     document.querySelector('.bde-post-loop') ||
                     document.querySelector('.oxy-post-loop');
          if (!list) { console.warn('HCS: course loop nahi mila'); return; }

          var rows = Array.prototype.slice.call(list.querySelectorAll('article'));
          var countEl = document.querySelector('.hcs-count');

          function getVal(row, attr) {
            var el = row.querySelector('[' + attr + ']');
            return el ? el.getAttribute(attr) : '';
          }
          function norm(v)   { return (v || '').toString().trim().toLowerCase(); }
          function digits(v) { return (v || '').toString().replace(/\D/g, ''); }

          function apply() {
            var c  = courseSel ? courseSel.value : 'all';
            var ci = citySel   ? citySel.value   : 'all';
            var f  = formatSel ? formatSel.value : 'all';
            var d  = dateInp   ? digits(dateInp.value) : '';
            var visible = 0;

            rows.forEach(function (row) {
              var ok = true;
              if (c  !== 'all' && norm(c)  !== norm(getVal(row, 'data-course'))) ok = false;
              if (ci !== 'all' && norm(ci) !== norm(getVal(row, 'data-city')))   ok = false;
              if (f  !== 'all' && norm(f)  !== norm(getVal(row, 'data-format'))) ok = false;
              if (d) {
                var rd = digits(getVal(row, 'data-date'));
                if (!rd || rd < d) ok = false;
              }
              row.style.display = ok ? '' : 'none';
              if (ok) visible++;
            });

            if (countEl) {
              countEl.textContent = visible + ' session' + (visible === 1 ? '' : 's') + ' matching your filters';
            }
          }

          [courseSel, citySel, formatSel, dateInp].forEach(function (el) {
            if (el) el.addEventListener('change', apply);
          });

          apply();
        });
        </script>
        <?php
    }
}

/**
 * Front-end CSS (head me load hota hai)
 */
if ( ! function_exists( 'hcs_course_finder_css' ) ) {

    add_action( 'wp_head', 'hcs_course_finder_css', 99 );

    function hcs_course_finder_css() {
        ?>
        <style>
        /* Filter bar */
        .hcs-finder-title { font-size: 22px; font-weight: 700; margin-bottom: 16px; }
        .hcs-finder-grid { display: grid; gap: 16px; grid-template-columns: repeat(4, 1fr); }
        @media (max-width: 768px) { .hcs-finder-grid { grid-template-columns: 1fr; } }
        .hcs-field label { display:block; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:6px; }
        .hcs-field select, .hcs-field input { width:100%; min-height:44px; padding:8px 12px; border:1px solid #e5e7eb; border-radius:4px; font-size:14px; background:#fff; }
        .hcs-count { margin-top:20px; font-size:14px; color:#6b7280; }

        /* List */
        .hcs-list { list-style: none; margin: 0; padding: 0; }
        .hcs-row { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 20px; padding: 14px 0; border-bottom: 1px solid #e5e7eb; }
        .hcs-name { font-weight: 600; flex: 1; min-width: 180px; }
        .hcs-meta, .hcs-seats { font-size: 14px; color: #6b7280; }
        .hcs-badge { font-size: 11px; font-weight: 600; text-transform: uppercase; border: 1px solid #e5e7eb; border-radius: 999px; padding: 3px 10px; }
        .hcs-seats { color: #d11a2a; font-weight: 600; }
        .hcs-btn-outline, .hcs-btn { padding: 8px 14px; border-radius: 4px; font-size: 14px; font-weight: 600; text-decoration: none; display:inline-flex; align-items:center; }
        .hcs-btn-outline { border: 1px solid rgba(15,32,63,.3); color: #0f203f; }
        .hcs-btn { background: #0f203f; color: #fff; }
        .hcs-empty { color:#6b7280; }
        </style>
        <?php
    }
}