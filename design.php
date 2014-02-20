<? include "_head.php" ?>

<p>All configure-related files (except for ./configure, which is just a wrapper script)
are stored in cnf/ directory. For the sake of clarity/readability, bash
functions are used. This can be changed to "set ...; eval $function"
constructs (or m4 macros) later.</p>

<p>Actual writing to config.sh occurs at the very end of configure script. Until
then, all values are stored in shell variables (with exactly the same names
as in config.sh). Once a variable is set to non-empty value, it won't be changed
(most of the time). If some test should be performed to set a variable but
that variable is already set, the test will be skipped.<br>
Hint files do not override values preset by user, but may override other hinted values.</p>

<p>Hint file format is different from those used in traditional scripts.<br>
Variable values should be assigned like this:
<pre>
variable=value
variable+=value
</pre>
but note the file will be post-processed and these will not remain simple assignments.
See <a href="hints.html">hints page</a> on this.</p>


<h3>configure files</h3>

<p>cnf/configure is the entry point, and the only file that calls other configure_* files.<br>
cnf/configure__.sh defines common functions. Test-specific functions are defined
in resp. test files. Finally, cnf/configure_genc.sh is called to write variables
to config.sh.</p>


<h3>configure variables</h3>

<p>Most variables from config.sh are described in Porting/Glossary in the perl source.
Make sure to check that file.</p>

<p>Within configure itself, single-letter and underscored single-letter variables are "local"
and should only be used within a single function.</p>

<p>configure does set some global variables which do not go to config.sh; $config, $cfglog, $mode,
$loadfile and so on.</p>


<h3>Patching perl files</h3>

<p>There are some minor changes perl-cross needs in the original perl files.
Starting with perl-cross-0.7, relevant patches are supplied in cnf/diffs directory.</p>

<p>The patches are applied by <tt>crosspatch</tt> make target (which is the first one
in <tt>make all</tt> sequence).
For each successfully applied <tt>cnf/diffs/file.patch</tt> a lock file,
<tt>cnf/diffs/file.applied</tt>, is created, so the patches are not applied twice.<br>
Note: the above applies to perl-cross-0.8; versions 0.7.x did it differently.</p>

<h3>Building miniperl</h3>

<p>miniperl should work on the build, not target, platform. It is compiled
using native compiler, unlike primary perl executable which is built later
using build-to-target cross-compiler. Because of this, during cross-build
configure is run <i>twice</i>, for build and for target platform.</p>

<p>Native and target build differ in file extensions: the latter uses usual .o
(default) but the former has .host.o instead. All object files are kept in
the same (root) directory.</p>

<p>Different configs are used for different platforms:
config.{h,sh} for the target build and xconfig.{h,sh} for build-time miniperl. 
<b>Beware</b>: this is exactly <i>opposite</i> of what the original Configure does,
The relation was inversed because most tools use config.sh by default, and cross-build
is viewed as primary. Among other things, building extensions with config.sh
is simpler, and extensions are not built for miniperl.</p>

<p>See also <a href="modules.html">Cross-compiling modules</a> on some issues
with config files.</p>


<h3>Build-time scripts</h3>

<p>Most of the time I tried to use scripts like configpm, utils/ext/* etc. unchanged,
despite the fact I don't like the whole idea very much. Maybe it will be the next
thing to change, but I want to get this first version working.</p>

<p>Some of the code used during build stage tries to load XS modules.
Most of the time, for mundane tasks like can_run() or tempfile() or strftime().
It's not acceptable for perl-cross which uses miniperl exclusively during build
process (unlike the native makefiles which can and do rely on newly-built perl),
and adding dynaloader support in miniperl is a bit too much.</p>

<p>Current solution is either to patch the offending scripts, or to provide minimalistic
pure-perl stubs for the required XS modules.<br>
Patches are kept in <tt>cnf/diffs</tt>, and stubs are in <tt>cnf/stub<tt>.<br>
In one particular case, Digest::MD5, a dynaloaded module was replaced with non-XS
equivalent Digest::MD5::Perl from CPAN.</p>

<p>The way configpm used to choose which files to update made no sense with the new
configure, so it was changed: --config-sh, --config-pm and --config-pod were added,
with default values set to config.sh, lib/Config.pm and lib/Config.pod.</p>

<? include "_foot.php" ?>
