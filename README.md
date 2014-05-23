Foundation Namespace Base
==========


Installation
------------

Add the repo as a submodule to your project.

Add a filter to the .gitattributes file inside the submodule folder, sample commend below (run from inside the submodule directory)

`echo '*.php filter=namespace' >> .gitattributes`

Add the filters to the git config for the submodule, sample for new namespace being "WPP\New_Project_Name\Base"

`git config filter.namespace.smudge "sed -e 's/WPP\\\\Foundation_Namespace_Base/WPP\\\\New_Project_Name\\\\Base/'"`

`git config filter.namespace.clean "sed -e 's/WPP\\\\New_Project_Name\\\\Base/WPP\\\\Foundation_Namespace_Base/'"`


Versions
--------

**1.0.0**

* Initial Release

**0.9.0**

* Initial prerelease
