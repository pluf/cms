<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;

require_once 'Pluf.php';

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Cms_Content_FieldsTest extends TestCase
{

    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/mysql.rest.conf.php');
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Group',
            'Role',
            'CMS'
        ));
        $m->install();
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Group',
            'Role',
            'CMS'
        ));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function shouldSetMimeType()
    {
        $mime_type = 'application/test';
        // Get by id
        $content = new CMS_Content();
        $content->name = 'test content' . rand();
        $content->mime_type = $mime_type;
        $content->create();
        Test_Assert::assertFalse($content->isAnonymous(), 'Object is not created');
        Test_Assert::assertEquals($mime_type, $content->mime_type);
    }
    /**
     * @test
     */
    public function shouldKeepMimeTypeInUpdate()
    {
        $mime_type = 'application/test';
        // Get by id
        $content = new CMS_Content();
        $content->name = 'test content' . rand();
        $content->mime_type = $mime_type;
        $content->create();
        Test_Assert::assertFalse($content->isAnonymous(), 'Object is not created');
        Test_Assert::assertEquals($mime_type, $content->mime_type);
        
        $content2 = new CMS_Content($content->id);
        Test_Assert::assertFalse($content2->isAnonymous(), 'Object is not created');
        Test_Assert::assertEquals($mime_type, $content2->mime_type);
        
        $content2->download = 10;
        $content2->update();
        
        $content3 = new CMS_Content($content->id);
        Test_Assert::assertFalse($content3->isAnonymous(), 'Object is not created');
        Test_Assert::assertEquals($mime_type, $content3->mime_type);
    }
}

