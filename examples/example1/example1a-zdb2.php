<?php
/**
 * Example 1
 *
 * PHP Version 5.3
 *
 * @category   Tracks
 * @package    Examples
 * @subpackage Example1
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'bootstrap.php';
require_once 'Employer.php';
require_once 'Employee.php';
require_once 'Position.php';
require_once 'Welcomer.php';

$router = new \Tracks\EventHandler\DirectRouter;
$router->addHandler('EventEmployeeAdded', 'Welcomer');

$repository = new \Tracks\EventStore\Repository(
    new \Tracks\EventStore\EventStorage\ZendDb2(
        new \Zend\Db\Adapter\Adapter(
            array(
                'driver' => 'Pdo_Mysql',
                'database' => 'ddd',
                'username' => 'ddd',
                'password' => 'ddd',
                'hostname' => '192.168.33.14'
            )
        )
    ),
    $router,
    new \Tracks\EventStore\SnapshotStorage\Memory
);

$employer = new Employer;
$employerGuid = $employer->create('Planet Express');
$leelaGuid = $employer->addNewEmployee('Turanga Leela', 'Captain');
$fryGuid = $employer->addNewEmployee('Philip Fry', 'Delivery Boy');

$repository->save($employer);


$employer = $repository->load($employerGuid);
$employer->changeEmployeeTitle($fryGuid, 'Narwhal Trainer');

$repository->save($employer);

echo PHP_EOL.$employer->name.PHP_EOL;
var_dump($employer);
foreach ($employer->employees as $employee) {
    echo '  - '.$employee->name.', '.$employee->position->title.PHP_EOL;
}

