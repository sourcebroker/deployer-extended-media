deployer-extended-media
=======================

.. image:: https://styleci.io/repos/94532515/shield?branch=master
   :target: https://styleci.io/repos/94532515

.. image:: https://scrutinizer-ci.com/g/sourcebroker/deployer-extended-media/badges/quality-score.png?b=master
   :target: https://scrutinizer-ci.com/g/sourcebroker/deployer-extended-media/?branch=master

.. image:: http://img.shields.io/packagist/v/sourcebroker/deployer-extended-media.svg?style=flat
   :target: https://packagist.org/packages/sourcebroker/deployer-extended-media

.. image:: https://img.shields.io/badge/license-MIT-blue.svg?style=flat
   :target: https://packagist.org/packages/sourcebroker/deployer-extended-media

|

.. contents:: :local:

What does it do?
----------------

The package provides additional tasks for deployer (deployer.org) for synchronizing media between instances.

How this can be useful for me?
------------------------------

The most useful is ``dep media:pull [source]`` task which allows you to pull media from source instance to current
instance with rsync.

There are also two additional useful tasks which allows to copy or symlink media between remote instances. For example
you can use ``dep media:link [source] --options=target:[target]`` to create symlinks for each single file (equivalent of cp -Rs).

Installation
------------

1) Install package with composer:
   ::

      composer require sourcebroker/deployer-extended-media

2) If you are using deployer as composer package then just put following line in your deploy.php:
   ::

      new \SourceBroker\DeployerLoader\Load([['path' => 'vendor/sourcebroker/deployer-extended-media/deployer']]);

3) If you are using deployer as phar then put following lines in your deploy.php:
   ::

      require_once(__DIR__ . '/vendor/sourcebroker/deployer-loader/autoload.php');
      new \SourceBroker\DeployerLoader\Load([['path' => 'vendor/sourcebroker/deployer-extended-media/deployer']]);

   | IMPORTANT NOTE!
   | Do not put ``require('/vendor/autoload.php')`` inside your deploy.php because you can have dependency problems.
     Use ``require_once(__DIR__ . '/vendor/sourcebroker/deployer-loader/autoload.php');`` instead as suggested.

4) In deploy.php set the folders you want to synchronize:
   ::

      set('media',
          [
           'filter' => [
               '+ /fileadmin/',
               '- /fileadmin/_processed_/*',
               '+ /fileadmin/**',
               '+ /uploads/',
               '+ /uploads/**',
               '- *'
          ]
      ]);

5) Run the task:
   ::

      dep media:pull [source]

Options
-------

- | **exclude**
  | *default value:* null
  |
  | Array with patterns to be excluded.

  |
- | **exclude-case-insensitive**
  | *default value:* null
  |
  | Array with patterns to be excluded. Because rsync does not support case insensitive then
    each value of array is set in state uppercase/lowercase. That means if you will have ``['*.mp4', '*.zip']``
    then final exclude will be ``--exclude '*.[mM][pP]4' --exclude '*.[zZ][iI][pP]'``

  |
- | **exclude-file**
  | *default value:* null
  |
  | String containing absolute path to file, which contains exclude patterns.

  |
- | **include**
  | *default value:* null
  |
  | Array with patterns to be included.

  |
- | **include-file**
  | *default value:* null
  |
  | String containing absolute path to file, which contains include patterns.

  |
- | **filter**
  | *default value:* null
  |
  | Array of rsync filter rules

  |
- | **filter-file**
  | *default value:* null
  |
  | String containing merge-file filename.

  |
- | **filter-perdir**
  | *default value:* null
  |
  | String containing merge-file filename to be scanned and merger per each directory in rsync
    list offiles to send.

  |
- | **flags**
  | *default value:* rz
  |
  | Flags added to rsync command.

  |
- | **options**
  | *default value:* ['copy-links', 'keep-dirlinks', 'safe-links']
  |
  | Array of options to be added to rsync command.

  |
- | **timeout**
  | *default value:* 0
  |
  | Timeout for rsync task. Zero means no timeout.


Default configuration for task:
::
   set('media_default',
    [
        'exclude' => [],
        'exclude-case-insensitive' => [
            '*.mp4',
            '*.zip',
            '*.pdf',
            '*.exe',
            '*.doc',
            '*.docx',
            '*.pptx',
            '*.ppt',
            '*.xls',
            '*.xlsx',
            '*.xlsm',
            '*.tiff',
            '*.tif',
            '*.potx',
            '*.mpg',
            '*.mp3',
            '*.avi',
            '*.wmv',
            '*.flv',
            '*.eps',
            '*.ai',
            '*.mov',
        ],
        'exclude-file' => false,
        'include' => [],
        'include-file' => false,
        'filter' => [],
        'filter-file' => false,
        'filter-perdir' => false,
        'flags' => 'rz',
        'options' => ['copy-links', 'keep-dirlinks', 'safe-links'],
        'timeout' => 0,
    ]);


In your project you should set "media" which will be merged with "media_default" configuration.

Example configuration for TYPO3 CMS (typo3.org):
::

   set('media',
       [
        'filter' => [
            '+ /fileadmin/',
            '- /fileadmin/_processed_/*',
            '+ /fileadmin/**',
            '+ /uploads/',
            '+ /uploads/**',
            '- *'
       ]
   ]);


Tasks
-----

media:copy
++++++++++

Copy media between (remote) instances.

::

    dep media:copy [source] --options=target:[target]

Commands are executed on target remote instance. If instances are placed on the same remote server then rsync on
local files are called. If instances are placed on different remote servers then ``media:pull [source]`` is executed
on target instance.

Example: ``dep media:copy live --options=target:beta``

media:link
++++++++++

Only for remote instances placed on same machine.
Command creates symbolic links on target instance pointing to files on source machine.

::

    media:link [source] --options=target:[target]

For each file from source instance that does not exist on target instance:
1. Create directory tree recursively.
2. Symlink to file from source instance.

So each file on target instance may be modified / deleted without effect on source.

Example: ``dep media:link live --options=target:beta``

media:pull
++++++++++

Pull media from source instance to current instance using rsync and options from "media_default" and "media".

::

    dep media:pull [source]

Example: ``dep media:pull live``

media:push
++++++++++

Pull media from current instance to target instance using rsync and options from "media_default" and "media".

::

    dep media:push [target]

Example: ``dep media:push beta``


Changelog
---------

See https://github.com/sourcebroker/deployer-extended-media/blob/master/CHANGELOG.rst
