<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineORMModule\Service\DBALConnectionFactory;

class OrmConnectionFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $dbalConnectionFactory = new DBALConnectionFactory('orm_default');
        $connection = $dbalConnectionFactory->createService($sl);

        $configuration = $connection->getConfiguration();
        $configuration->setFilterSchemaAssetsExpression('~^business.~');

        return $connection;
    }
}
