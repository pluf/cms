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
class User_Monitor_BasicsTest extends TestCase
{
    
    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__.'/../conf/mysql.monitor.conf.php');
        $m = new Pluf_Migration(array(
            'Pluf',
            'User',
            'Group',
            'Role',
            'CMS'
        ));
        $m->install();
        
        $user = new User();
        $user->login = 'test';
        $user->first_name = 'test';
        $user->last_name = 'test';
        $user->email = 'toto@example.com';
        $user->setPassword('test');
        $user->active = true;
        $user->administrator = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        
        $role = Role::getFromString('Pluf.owner');
        $user->setAssoc($role);
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
    public function getCountentCount()
    {
        
        $client = new Test_Client(array(
            array(
                'app' => 'User',
                'regex' => '#^/api/user#',
                'base' => '',
                'sub' => include 'User/urls.php'
            ),
            array(
                'regex' => '#^/monitor/count$#',
                'model' => 'CMS_Monitor',
                'method' => 'count',
                'http-method' => 'GET'
            )
        ));
        
        // Change detail
        $user = new User();
        $user = $user->getUser('test');
        
        // Login
        $response = $client->post('/api/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // count
        $response = $client->get('/monitor/count');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

}