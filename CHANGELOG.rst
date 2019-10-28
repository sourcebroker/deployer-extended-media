
Changelog
---------

master
~~~~~~
1) [TASK][BREAKING] Remove media:move as its replaced by better media:copy.
2) [TASK][BREAKING] Use sourcebroker/deployer-instance for instance data management.
3) [BUGFIX] Change colon to underscore for Windows compatibility

4.0.0
~~~~~

1) [TASK][BREAKING] Possible breaking change for those using global "dep" instead of that one in './vendor/bin/dep' as
   'local/bin/deployer' is set now to './vendor/bin/dep'.

3.0.0
~~~~~

a) [TASK] Add dependency to sourcebroker/deployer-loader
b) [TASK][!!!BREAKING] Remove SourceBroker\DeployerExtendedDatabase\Loader.php in favour of using sourcebroker/deployer-loader
c) [TASK][!!!BREAKING] Remove SourceBroker\DeployerExtendedDatabase\Utility\FileUtility because not used now.
d) [TASK] Add changelog
