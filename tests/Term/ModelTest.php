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
class Term_ModelTest extends TestCase
{
    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();
        $m->init();
        
        // Test user
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('test');
        if (true !== $credit->create()) {
            throw new Exception();
        }
        
        $per = User_Role::getFromString('tenant.owner');
        $user->setAssoc($per);
    }

    /**
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function createModelTest()
    {
        $item = new CMS_Term();
        $item->name = 'term-' . rand();
        $item->slug = 'slug-' . rand();
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_Term');
    }

    /**
     * @test
     */
    public function slugUniquenessTest()
    {
        $this->expectException(Pluf_Exception_BadRequest::class);
        
        $item = new CMS_Term();
        $item->name = 'term-' . rand();
        $item->slug = 'slug-' . rand();
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_Term');
        
        $item2 = new CMS_Term();
        $item2->name = 'term-' . rand();
        $item2->slug = $item->slug;
        Test_Assert::assertFalse($item2->create(), 'Two Term with the same slug should not be created');
    }
    

}


