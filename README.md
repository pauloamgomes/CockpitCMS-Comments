# Comments add-on for Cockpit CMS

This add-on enhances Cockpit CMS by providing a collaborative commenting mechanism to be used in the collections.
User can add new comments and reply to existing ones on collection entries.

## Installation

Download and unpack add-on to `<cockpit-folder>/addons/Comments` folder.

## Configuration

In order to use the addon, it's required to specifiy in the Cockpit config the collections we want to provide comments, e.g.:

```yaml
comments:
  collections:
    - page
    - post
    - categories
```
or if you want to enable in all collections:

```yaml
comments:
  collections: *
```

For non admin users its required to provide permissions in the configuration (view, add and delete), e.g.:

```yaml
groups:
  editor:
    comments:
      view: true
      add: true
      delete: true
```

## Usage

When enabled, the addon will provide a new button in the collections entry page.

[![Comments Screencast](https://monosnap.com/image/0tYIDVhWvmCmeGTi7Is390vFtzYM8C)](http://www.youtube.com/watch?v=L3yRtMEOcgA "Comments Screencast")


## Copyright and license

Copyright 2019 pauloamgomes under the MIT license.


