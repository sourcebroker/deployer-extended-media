
Changelog
---------

4.0.0.
~~~~~~

1) [TASK][BREAKING] Possible breaking change for those using global "dep" instead of that one in './vendor/bin/dep' as
   'local/bin/deployer' is set now to './vendor/bin/dep'.

3.0.0
~~~~~

a) [TASK] Add dependency to sourcebroker/deployer-loader
b) [TASK][!!!BREAKING] Remove SourceBroker\DeployerExtendedDatabase\Loader.php in favour of using sourcebroker/deployer-loader
c) [TASK][!!!BREAKING] Remove SourceBroker\DeployerExtendedDatabase\Utility\FileUtility because not used now.
d) [TASK] Add changelog