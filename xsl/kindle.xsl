<?xml version="1.0" encoding='utf8'?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="kindleGetters.xsl"/>
<xsl:output omit-xml-declaration="no" method="xml" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" encoding="utf8" indent="yes"/>

<xsl:template match="/caches">

<html xml:lang="en" lang="en">
<head>
<title>Geocaching for Kindle</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../../xsl/style.css" type="text/css" media="all" />
<xsl:apply-templates select="/" mode="includes"/>
</head>
<body>

<xsl:apply-templates select="/caches" mode="tocName"/>
<xsl:apply-templates select="/caches" mode="tocDifficulty"/>
<xsl:apply-templates select="/caches" mode="tocTerrain"/>

<xsl:for-each select="cache">
<div class="cache">
<span class="code">
	<xsl:apply-templates select="code"/>
</span>

<h2>
	<xsl:apply-templates select="name"/>
	[<xsl:apply-templates select="type"/>]
</h2>

<span class="author">
	by <xsl:apply-templates select="author"/>
</span>

<span class="link">
	<xsl:apply-templates select="link"/>
</span>

<span class="location">
	at <xsl:apply-templates select="location"/>
</span>

<span class="difficulty">
	D: <xsl:apply-templates select="difficulty"/>
</span>

<span class="terrain">
	T: <xsl:apply-templates select="terrain"/>
</span>

<span class="size">
	S: <xsl:apply-templates select="size"/>
</span>

<div class="shortDescription">
	<xsl:apply-templates select="short-description"/>
</div>

<div class="longDescription">
	<xsl:apply-templates select="long-description"/>
</div>


<div class="hint">
	<h3>Hint</h3>
	<xsl:apply-templates select="hintEncrypted"/>
	<xsl:apply-templates select="hintDecrypted"/>
	<xsl:apply-templates select="hintKey"/>
</div>

<div class="map">
	<xsl:apply-templates select="map"/>
</div>

<div class="waypoints">
	<xsl:apply-templates select="waypoint"/>
</div>


<div class="meta">
	<h3>Meta</h3>
	<xsl:apply-templates select="meta"/>
</div>

<div class="attributes">
	<h3>Attributes</h3>
	<xsl:apply-templates select="attributes"/>
</div>

<div class="logs">
	<h3>Logs</h3>
	<xsl:apply-templates select="log"/>
</div>

</div>

</xsl:for-each>

</body>
</html>

</xsl:template>


</xsl:stylesheet>
