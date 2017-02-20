<?php

namespace Scripts;

use Composer\Installer\InstallerEvent;
use Composer\Json\JsonFile;
use Composer\Semver\VersionParser;
use Composer\Repository\RepositoryFactory;
use Composer\Factory;
use Composer\Script\Event;

class Composer
{
    public static function addSubreposDependencies(InstallerEvent $event)
    {
        $autoloadDev = $event->getComposer()->getPackage()->getDevAutoload();

        if (isset($autoloadDev['psr-4'])) {
            $autoloadDevPackages = $autoloadDev['psr-4'];

            foreach ($autoloadDevPackages as $name => $folder) {
                $composerJsonPath = dirname($folder) . '/composer.json';

                if (file_exists($composerJsonPath)) {
                    $jsonFile = new JsonFile($composerJsonPath);
                    $composerJson = $jsonFile->read();

                    if (isset($composerJson['require'])) {
                        $versionsParser = new VersionParser();

                        foreach ($composerJson['require'] as $package => $version) {
                            $constraint = $versionsParser->parseConstraints($version);
                            $event->getRequest()->install($package, $constraint);
                        }
                    }
                }
            }
        }
    }

    public static function addSubreposRepositories(Event $event)
    {
        $autoloadDev = $event->getComposer()->getPackage()->getDevAutoload();

        if (isset($autoloadDev['psr-4'])) {
            $autoloadDevPackages = $autoloadDev['psr-4'];

            foreach ($autoloadDevPackages as $name => $folder) {
                $composerJsonPath = dirname($folder) . '/composer.json';

                if (file_exists($composerJsonPath)) {
                    $jsonFile = new JsonFile($composerJsonPath);
                    $composerJson = $jsonFile->read();

                    if (isset($composerJson['repositories'])) {
                        $composer = $event->getComposer();
                        $repositoryManager = $composer->getRepositoryManager();
                        $io = $event->getIo();
                        $config = Factory::createConfig($io);

                        foreach ($composerJson['repositories'] as $repositoryData) {
                            $repository = RepositoryFactory::createRepo(
                                $io,
                                $config,
                                $repositoryData
                            );

                            $repositoryManager->addRepository($repository);
                        }
                    }
                }
            }
        }
    }
}
