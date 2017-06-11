# plop-catcher
Make errors and logging and etc much nicer.

Better error catching and logging. Usage:

Plop($outputMode, $callbackFunction);

$outputMode can be 'HTML', 'JSON' or 'ARRAY'
$callbackFunction will receive html, json or a PHP array, and is called if a fatal error uncaught exception stops execution allowing graceful error screens or debug output.

~~~~
$plop = new \DanielJHarvey\PlopCatcher\Plop('HTML',function($html) {
    outputHTML($html);
});
$plop->enable();

// code that will be executed

// blah blah blah blah blah blah

// end of code

// output debugging info in chosen format
outputHTML($plop->output());

function outputHTML(html) {
    echo $html;
}
~~~~

Other things

~~~~
$plop->logComment($message);
~~~~
