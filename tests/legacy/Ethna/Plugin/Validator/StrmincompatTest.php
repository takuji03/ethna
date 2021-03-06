<?php
/*
 * Copyright (C) the Ethna contributors. All rights reserved.
 *
 * This file is part of the Ethna package, distributed under new BSD.
 * For full terms see the included LICENSE file.
 */

class Ethna_Plugin_Validator_Strmincompat_Test extends PHPUnit_Framework_TestCase
{
    private $vld;

    public function setUp()
    {
        $this->controller = new Ethna_Controller_Dummy();
        $this->controller->setClientEncoding('EUC-JP');
        $this->form = new Ethna_ActionForm_Dummy($this->controller);
        $this->controller->setActionForm($this->form);
        $this->ae = new Ethna_ActionError();
        $plugin = $this->controller->getPlugin();
        $this->vld = $plugin->getPlugin('Validator', 'Strmincompat');
    }

    public function tearDown()
    {
        unset($GLOBALS['_Ethna_controller']);
    }

    public function test_min_str_compat_euc()
    {
        if (extension_loaded('mbstring')) {
            $form_str = array(
                'type'          => VAR_TYPE_STRING,
                'required'      => true,
                'strmincompat'  => '4',  // 半角4、全角2文字
            );
            $af = $this->controller->getActionForm();
            $af->setDef('namae_str', $form_str);

            //    ascii.
            $input_str = 'abcd';
            $pear_error = $this->vld->validate('namae_str', $input_str, $form_str);
            $this->assertFalse($pear_error instanceof Ethna_Error);

            $error_str = 'abc';
            $pear_error = $this->vld->validate('namae_str', $error_str, $form_str);
            $this->assertTrue($pear_error instanceof Ethna_Error);
            $this->assertEquals(E_FORM_MIN_STRING, $pear_error->getCode());

            //    multibyte string
            $input_str = 'あい';
            $input_str_euc = mb_convert_encoding($input_str, 'EUC-JP', 'UTF-8');
            $pear_error = $this->vld->validate('namae_str', $input_str_euc, $form_str);
            $this->assertFalse(is_a($pear_error, 'Ethna_Error'));

            $error_str = 'あ';
            $error_str_euc = mb_convert_encoding($error_str, 'EUC-JP', 'UTF-8');
            $pear_error = $this->vld->validate('namae_str', $error_str_euc, $form_str);
            $this->assertTrue($pear_error instanceof Ethna_Error);
            $this->assertEquals(E_FORM_MIN_STRING,$pear_error->getCode());

        } else {
            echo " ... skipped because mbstring extension is not installed.";
        }

        //  TODO: Error Message Test.
    }

    public function test_min_str_compat_sjis()
    {
        if (extension_loaded('mbstring')) {

            $this->controller->setClientEncoding('SJIS');

            $form_str = array(
                'type'          => VAR_TYPE_STRING,
                'required'      => true,
                'strmincompat'  => '4',  // 半角4、全角2文字
            );
            $af = $this->controller->getActionForm();
            $af->setDef('namae_str', $form_str);

            //    ascii.
            $input_str = 'abcd';
            $pear_error = $this->vld->validate('namae_str', $input_str, $form_str);
            $this->assertFalse($pear_error instanceof Ethna_Error);

            $error_str = 'abc';
            $pear_error = $this->vld->validate('namae_str', $error_str, $form_str);
            $this->assertTrue($pear_error instanceof Ethna_Error);
            $this->assertEquals(E_FORM_MIN_STRING,$pear_error->getCode());

            //    multibyte string(sjis)
            $input_str = 'あい';
            $input_str_sjis = mb_convert_encoding($input_str, 'SJIS', 'UTF-8');
            $pear_error = $this->vld->validate('namae_str', $input_str_sjis, $form_str);
            $this->assertFalse($pear_error instanceof Ethna_Error);

            $error_str = 'あ';
            $error_str_sjis = mb_convert_encoding($error_str, 'SJIS', 'UTF-8');
            $pear_error = $this->vld->validate('namae_str', $error_str_sjis, $form_str);
            $this->assertTrue($pear_error instanceof Ethna_Error);
            $this->assertEquals(E_FORM_MIN_STRING,$pear_error->getCode());

        } else {
            echo " ... skipped because mbstring extension is not installed.";
        }

        //  TODO: Error Message Test.
    }
}

