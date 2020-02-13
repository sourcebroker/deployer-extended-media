
Changelog
---------

6.0.3
~~~~~

1) [BUGFIX] Fix media:push task.

6.0.2
~~~~~

1) [BUGFIX] Fix dependencies to "sourcebroker/deployer-loader" and "sourcebroker/deployer-instance".

6.0.1
~~~~~

1) [BUGFIX] Docs fix.

6.0.0
~~~~~

1) [TASK][BREAKING] Compatibility to Deployer 6.

5.0.0
~~~~~

1) [TASK][BREAKING] Remove media:move as its replaced by better media:copy.
2) [TASK][BREAKING] Use sourcebroker/deployer-instance for instance data management.
3) [BUGFIX] Change colon to underscore for Windows compatibility
4) [TASK] Replace RuntimeException with GracefulShutdownException.
5) [TASK] Increase version of sourcebroker/deployer-instance.
6) [TASK] Introduce var for hardcoded instances names.
7) [TASK] Use Configuration object from sourcebroker/instance.
8) [TASK] Improve task descriptions.
8) [BUGFIX] media:copy should push when doing synchro on different instances.

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
