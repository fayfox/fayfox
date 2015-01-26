CKEDITOR.dialog.add('Code', function(editor){
	var encode = function(str){
		var s = "";
		if (str == undefined || str.length == 0) return "";
		s = str.replace(/&/g, "&amp;");
		s = s.replace(/</g, "&lt;");
		s = s.replace(/>/g, "&gt;");
		s = s.replace(/ /g,"&nbsp;");
		s = s.replace(/\'/g, "&#39;");
		s = s.replace(/\"/g, "&quot;");
		s = s.replace(/\n/g, "<br>");
		return s;
	};
	return { 
		title : '插入程序代码',
		minWidth : 600,
		minHeight : 200,
		contents : [{
            id: 'cb',
            name: 'cb',
            label: 'cb',
            title: 'cb',
			elements: [{
				type: 'select',
				id: 'lang',
				required: true,
				'default': 'php',
				items: [['PHP', 'php'], ['Javascript', 'js'], ['HTML', 'html'], ['CSS', 'css'], ['XML', 'xml']]
			}, {
				type: 'textarea',
				id: 'code',
				rows: 20,
				'default': ''
			}]
		}],
		onOk: function(){
			editor.insertHtml('<pre class="prettyprint lang-' + this.getValueOf('cb', 'lang') + '">' + encode(this.getValueOf('cb', 'code')) + '</pre>');
		}
	};
} );