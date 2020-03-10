<?php
$p = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n
<markers>
<marker id=\"1\" name=\"Billy Kwong\" address=\"1/28 Macleay Street, Elizabeth Bay, NSW\" lat=\"-33.869843\" lng=\"-151.225769\" type=\"restaurant\"/>\n
<marker id=\"2\" name=\"Love.Fish\" address="580 Darling Street, Rozelle, NSW" lat="-33.861034" lng="151.171936" type="restaurant"/>\n
</markers>";
$a = fopen("data/data.xml", 'w');
fwrite($a, $p);
fclose($a);
chmod("data/data.xml", 0644);
?>