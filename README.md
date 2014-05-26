Foundation Namespace Base
==========


Installation
------------

We recomended installing with a task management tool like grunt or gulp that supports [Lo-Dash templates](http://lodash.com/docs/#template)

Create a task to copy and apply your desired changes, the following vars has been used:

* <%= php_namespace_base_classes %>

**OR**

If you want to make life complicated you can find and replace "<%= php_namespace_base_classes %>" with your namespace for the base classes

**OR**

Lets complicate git and install a filter! _(have fun when adding a submodule to a submodule and then auto deplaying it)_

Add the repo as a submodule to your project.

Add a filter to the .gitattributes file inside the submodule folder, sample commend below (run from inside the submodule directory)

`echo '*.php filter=namespace' >> .gitattributes`

Add the filters to the git config for the submodule, sample for new namespace being "WPP\New_Project_Name\Base"

`git config filter.namespace.smudge "sed -e 's/<%= php_namespace_base_classes %>/WPP\\\\New_Project_Name\\\\Base/'"`

`git config filter.namespace.clean "sed -e 's/WPP\\\\New_Project_Name\\\\Base/<%= php_namespace_base_classes %>/'"`


Versions
--------

**1.0.0**

* Initial Release

**0.9.0**

* Initial prerelease
