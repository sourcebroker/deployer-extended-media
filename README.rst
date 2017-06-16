deployer-extended-database
==========================

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

The most useful is "dep media:pull [target]" task which allows you to pull media from target instance.
Having possibility to fast synchronise media can speed up instance dependent development.

Installation
------------
::

   composer require sourcebroker/deployer-extended-media


Task's documentation
--------------------

media
~~~~~

Options:

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
   set('media-default',
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


In your project you should set "media" which will be merged with "media-default" configuration.

Example configuration for TYPO3:
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


media:move
++++++++++

Move media from target instance to second target instance using rsync and options from "media-default" and "media".

Its a shortcut for two separated commands.
::

   media:move target1 target2


Is in fact:
::

   media:pull target1
   media:push target2

**Notice!**

Media are not moved directly from target1 to target2. First its synchronised from target1 instance to current
instance and then from current instance to target2 instance.

media:pull
++++++++++

Pull media from target instance to current instance using rsync and options from "media-default" and "media".

media:push
++++++++++

Pull media from current instance to target instance using rsync and options from "media-default" and "media".

