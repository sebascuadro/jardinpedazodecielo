<?php
/*  Copyright 2014 MarvinLabs (contact@marvinlabs.com) */

if (!class_exists('CUAR_ACFIntegrationInstaller')) {

    /**
     * Class that handles installing and deinstalling the add-on
     *
     * @author Vincent Prat @ MarvinLabs
     */
    class CUAR_ACFIntegrationInstaller
    {

        public static function on_deactivate()
        {
            cuar()->clear_attention_needed('acf-plugin-missing');
        }
    }
}