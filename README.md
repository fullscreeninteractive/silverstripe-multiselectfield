# MultiSelectField 

```
This is a fork of the http://svn.gpmd.net/svn/open/multiselectfield/tags/0.2/ 
which has been updated for 3.1. 
```

## Maintainer Contact

 * Will Rossiter (Nickname: wilr) <will@fullscreen.io>

## Requirements

* SilverStripe 3.1

## Overview

![Example](https://www.evernote.com/shard/s6/sh/ab40de01-9635-448c-93c7-96432e6d2ebb/0e58dd01a55a201fec50919f5f07d632/res/731c364e-e6aa-40e0-b50b-0636b3d6f6aa/skitch.png?resizeSmall)

A FormField for users to select and remove multiple items to a record. Similar 
to CheckboxSetField this handles both adding and removing entries backed by 
relations (has_many, many_many) and saving text strings as comma separated list.

## Installation
	
```
composer require "fullscreeninteractive/silverstripe-multiselectfield:dev-master"
```

## Usage Overview

### Relation

```
private static $many_many = array (
	'Departments' => 'Department'
);

..

$fields->push(new MultiSelectField(
    "Departments",
    "Departments",
    Departments::get()->map('ID', 'Title')
));
```

### Comma separated list

```
private static $db = array (
	'Departments' => 'Text'
);

..

$fields->push(new MultiSelectField(
    "Departments",
    "Departments",
    array(
    	'Design',
    	'Development',
    	'HR'
    )
));
```
