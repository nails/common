<?php

return array(
    'services' => array(
        'Asset' => function() {
            return new \Nails\Common\Library\Asset();
        },
        'Meta' => function() {
            return new \Nails\Common\Library\Meta();
        },
        'UserFeedback' => function() {
            return new \Nails\Common\Library\UserFeedback();
        },
        'Session' => function() {

            $oCi = get_instance();

            /**
             * STOP! Before we load the session library, we need to check if we're using
             * the database. If we are then check if `sess_table_name` is "nails_session".
             * If it is, and NAILS_DB_PREFIX != nails_ then replace 'nails_' with NAILS_DB_PREFIX
             */

            $sSessionTable = $oCi->config->item('sess_table_name');

            if ($sSessionTable === 'nails_session' && NAILS_DB_PREFIX !== 'nails_') {

                $sSessionTable = str_replace('nails_', NAILS_DB_PREFIX, $sSessionTable);
                $oCi->config->set_item('sess_table_name', $sSessionTable);
            }

            /**
             * Test that $_SERVER is available, the session library needs this
             * Generally not available when running on the command line. If it's
             * not available then load up the faux session which has the same methods
             * as the session library, but behaves as if logged out - comprende?
             */

            if ($oCi->input->server('REMOTE_ADDR')) {

                $oCi->load->library('session');

            } else {

                $oCi->load->library('auth/faux_session', 'session');
            }

            return $oCi->session;
        },
        'Encrypt' => function() {

            $oCi = get_instance();
            $oCi->load->library('encrypt');

            return $oCi->encrypt;
        },
        'Logger' => function() {

            $oCi = get_instance();
            $oCi->load->library('logger');

            return $oCi->logger;
        },
        'Database' => function() {

            $oCi = get_instance();
            $oDb = $oCi->load->database();

            if (empty($oDb->conn_id)) {

                throw new \Nails\Common\Exception\FactoryException(
                    'Failed to connect to database',
                    0
                );
            }

            /**
             * Don't run transactions in strict mode. In my opinion it's odd behaviour:
             * When a transaction is committed it should be the end of the story. If it's
             * not then a failure elsewhere can cause a rollback unexpectedly. Silly CI.
             */

            $oCi->db->trans_strict(false);

            return $oCi->db;
        },
    ),
    'factories' => array(
        'DateTime' => function() {
            return new \DateTime();
        }
    )
);
