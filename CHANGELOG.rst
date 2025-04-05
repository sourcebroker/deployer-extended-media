
Changelog
---------

14.0.0
------

1) [TASK][BREAKING] Use new version of ``sourcebroker/deployer-instance``. This version has fallback when no .env (or .env.local)
   file exists. It will take the system variables then. This should not break anything, but anyway a major version just to be sure.

13.1.0
------

1) [TASK] Extend the dependency to v5 of ``sourcebroker/deployer-loader``.

13.0.0
~~~~~~

1) [TASK][BREAKING] Add support for resolving home (``~``) in ``deploy_path``. Probably not breaking change but safer
    that each can test it before upgrading.

12.0.1
~~~~~~

1) [BUGFIX] Set default values for arguments in mergeRecursiveWithOverrule.

12.0.0
~~~~~~

1) [FEATURE] Add new merging strategy for media config. It now merges ``media_default``, ``media`` and ``media_custom``.
   In ``deploy.php`` user should use ``media_custom`` to override ``media_default`` and ``media``.

11.0.1
~~~~~~

1) [BUGFIX] Fix rsync ssh non standard options.

11.0.0
~~~~~~

1) [TASK][BREAKING] Bump dependency to ``sourcebroker/deployer-instance``.
2) [BUGFIX] Fix wrong method call for ssh options on ``media-pull`` na ``media-push``.

10.0.0
~~~~~~

1) [TASK][BREAKING] Refactor for Deployer 7.
2) [TASK] Extend dependency to internal packages to dev-master.

9.0.0
~~~~~

1) [TASK][BREAKING] Update dependency to ``sourcebroker/deployer-loader`` which introduce load folder/files
   alphabetically.
2) [TASK] Remove dropped styleci support.

8.0.0
~~~~~

1) [BUGFIX] Fix wrong namespace.
2) [TASK] Use more strict comparison.
3) [TASK] Add ddev.
4) [TASK][BREAKING] Increase dependency to `sourcebroker/deployer-instance`.

7.1.0
~~~~~~

1) [FEATURE] Use "release" folder for push/pull if available when calling those task.
2) [TASK] Apply Style CI fixes.

7.0.1
~~~~~~

1) [BUGFIX] Fix for normalize file regexp.

7.0.0
~~~~~

1) [TASK][BREAKING] Add protections switches (media_allow_pull_live, media_allow_push_live, media_allow_link_live,
   media_allow_copy_live) to allow push, copy to live instance.
2) [TASK] Protect copying/pushing/pulling media to top level instance.
3) [BUGFIX] Fix normalizeFilename regexp.

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
9) [BUGFIX] media:copy should push when doing synchro on different instances.

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
