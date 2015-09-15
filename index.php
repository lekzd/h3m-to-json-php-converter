<style>
	body {
		font-family: "Lucida Console", Monaco, monospace;
		line-height: 1.4em;
	}

	table {
		border-collapse: collapse;
		border: none;
		min-width: 400px;
	}

	table td {
		border: 1px solid #C7C7C7;
		border-left: none;
		border-right: none;
		padding: 5px;
	}

	table tr:nth-child(even) td {
		background: #F0F0F0;
	}

</style>

<?

require("parser/main.php");

new map_parser( ($_GET['file'])? $_GET['file'] : 'test_caves_emty' );

?>