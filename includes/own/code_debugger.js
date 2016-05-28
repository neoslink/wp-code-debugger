var editor = CodeMirror.fromTextArea(document.getElementById("textarea-debug"), {
	lineNumbers: true,
	matchBrackets: true,
	mode: "text/x-php",
	indentUnit: 4,
	indentWithTabs: true
});