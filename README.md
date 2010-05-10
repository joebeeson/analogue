# Analogue plugin for CakePHP 1.3+

Renames, or "maps", helpers prior to rendering so that you can easily replace helpers with your extended, or updated version.

## Installation

* Download the plugin

        $ cd /path/to/your/app/plugins && git clone git://github.com/joebeeson/analogue.git

* Add the helper to your `$helpers` and configure it there

        public $helpers = array(
           'Analogue' => array(
              array(
               'helper' => 'MyHtml',
               'rename' => 'Html'
              ),array(
               'helper' => 'MyForm',
               'rename' => 'Form'
              )
          )
        );

This will take the helpers, MyHtmlHelper and MyFormHelper and remap them to the Html and Form variables when rendering begins.
