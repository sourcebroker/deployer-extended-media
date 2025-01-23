deployer-extended-media
=======================

.. image:: https://img.shields.io/packagist/v/sourcebroker/deployer-extended-media.svg?style=flat
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

      set('media_custom',
          [
           'filter' => [
               '+ /public/',
               '+ /public/fileadmin/',
               '- /public/fileadmin/_processed_/*',
               '+ /public/fileadmin/**',
               '+ /public/uploads/',
               '+ /public/uploads/**',
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


In your deploy.php you should set ``media_custom`` which will be merged with ``media_default`` configuration.
See "Managing the media config" section to know how to set ``media_custom`` configuration.


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

Copy to instance defined in ``instance_live_name`` (default ``live``) is special case.
If you copy to highest instance then by default you will be asked twice if you really want to.
You can disable asking by setting ``media_allow_copy_live_force`` to ``true``.
You can also forbid coping to live instance by setting ``media_allow_copy_live`` to ``false``.

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

Linking to instance defined in ``instance_live_name`` (default ``live``) is special case.
If you link to highest instance then by default you will be asked twice if you really want to.
You can disable asking by setting ``media_allow_link_live_force`` to ``true``.
You can also forbid linking to live instance by setting ``media_allow_link_live`` to ``false``.

Example: ``dep media:link live --options=target:beta``

media:pull
++++++++++

Pull media from source instance to current instance using rsync and options from media config.

::

    dep media:pull [source]

Example: ``dep media:pull live``

Pulling to instance defined in ``instance_live_name`` (default ``live``) is special case.
If you pull to highest instance then by default you will be asked twice if you really want to.
You can disable asking by setting ``media_allow_pull_live_force`` to ``true``.
You can also forbid pulling to live instance by setting ``media_allow_pull_live`` to ``false``.

media:push
++++++++++

Pull media from current instance to target instance using rsync and options from media config.

::

    dep media:push [target]

Pushing to instance defined in ``instance_live_name`` (default ``live``) is special case.
If you push to highest instance then by default you will be asked twice if you really want to.
You can disable asking by setting ``media_allow_push_live_force`` to ``true``.
You can also forbid pushing to live instance by setting ``media_allow_push_live`` to ``false``.

Example: ``dep media:push beta``


Managing the media config
-------------------------

The final media config is result of merging three arrays:
 - ``media_default`` (from deployer-extended-media)
 - ``media`` (from deployer-extended-typo3)
 - ``media_custom`` (from user's deploy.php file)

The merging function has some special features:

1) A special ``__UNSET`` notation is used to remove specific items from array during the merging process.
2) An empty array will overwrite the array we merge to.

Examples of config final tunning
++++++++++++++++++++++++++++++++

**Example 1: Removing a specific option**

.. code-block:: php

    set('media_custom', [
        'options' => [
            '__UNSET' => ['safe-links'],
        ],
    ]);

In the above example, if ``options`` in the ``media_default`` array contained ``['copy-links', 'safe-links']``,
after the merge with ``media_custom``, ``options`` would contain only ``['copy-links']``.


**Example 2: Removing specific file types from exclusion**

.. code-block:: php

    set('media_custom', [
        'exclude-case-insensitive' => [
            '__UNSET' => ['*.pdf', '*.exe'],
        ],
    ]);

In this example, ``*.pdf`` and ``*.exe`` are removed from the list of case-insensitive excluded file types.


**Example 3: Completely clearing an array and adding one new option**

.. code-block:: php

    set('media_custom', [
        'exclude-case-insensitive' => [
            '__UNSET' => get('media_default')['exclude-case-insensitive'],
            '*.mp4'
        ],
    ]);

In this example, ``__UNSET`` is used to completely clear the ``exclude`` array in the ``media_default`` settings and add only ``*.mp4``.

If you want only to clear you can just set empty array:

.. code-block:: php

    set('media_custom', [
        'exclude-case-insensitive' => []
    ]);


**Example 4: Extend existing filter config**

.. code-block:: php

    set('media_custom', [
        'filter' => [
            '__UNSET' => ['- *'],
            '+ /' . get('web_path') . 'public/pim/',
            '+ /' . get('web_path') . 'public/pim/**',
            '- *',
        ],
    ]);

In this example, ``__UNSET`` is used to remove ``- *`` option from filter array, then adding ``public/pim`` folder
for synchronising. Finally putting ``- *`` at end of filter array.


Changelog
---------

See https://github.com/sourcebroker/deployer-extended-media/blob/master/CHANGELOG.rst
