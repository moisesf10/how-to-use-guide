import 'grapesjs/dist/css/grapes.min.css';
import grapesjs from 'grapesjs';
//import nl from 'grapesjs/locale/nl'
window.grapesjs = grapesjs;

import presetWebpage from 'grapesjs-preset-webpage';
import blockBasic from 'grapesjs-blocks-basic';
import grapesjsPluginForms from 'grapesjs-plugin-forms';
import grapesjsComponentContdown from 'grapesjs-component-countdown';
import grapesjsPluginExport from 'grapesjs-plugin-export';
import grapesjsTabs from 'grapesjs-tabs';
import grapesjsCustomCode from 'grapesjs-custom-code';
import grapesjsTouch from 'grapesjs-touch';
import grapesjsParserPostCss from 'grapesjs-parser-postcss';
import grapesjsTooltip from 'grapesjs-tooltip';
import grapesjsTuiImageEditor from 'grapesjs-tui-image-editor';
import grapesjsTyped from 'grapesjs-typed';
import grapesjsStyleBg from 'grapesjs-style-bg';
import grapesjsBlockTable from 'grapesjs-blocks-table';
import grapesjsCodeEditor from 'grapesjs-component-code-editor';

import ckeditor from 'grapesjs-plugin-ckeditor';



window.grapesJsPresetWebpage = presetWebpage;
window.grapesJsBlockBasic = blockBasic;
window.grapesjsPluginForms = grapesjsPluginForms;
window.grapesjsComponentContdown = grapesjsComponentContdown;
window.grapesjsPluginExport = grapesjsPluginExport;
window.grapesjsTabs = grapesjsTabs;
window.grapesjsCustomCode = grapesjsCustomCode;
window.grapesjsTouch = grapesjsTouch;
window.grapesjsParserPostCss = grapesjsParserPostCss;
window.grapesjsTooltip = grapesjsTooltip;
window.grapesjsTuiImageEditor = grapesjsTuiImageEditor;
window.grapesjsTyped = grapesjsTyped;
window.grapesjsStyleBg = grapesjsStyleBg;
window.grapesjsBlockTable = grapesjsBlockTable;
window.grapesjsCodeEditor = grapesjsCodeEditor;



window.CKEDITOR = ckeditor;

const editor =  grapesjs.init({
    container : '#gjs',
    fromElement: 1,
    plugins: [
        grapesJsBlockBasic,
        grapesjsPluginForms,
        grapesjsBlockTable,
        grapesjsComponentContdown,
        grapesJsPresetWebpage,
        grapesjsPluginExport,
        grapesjsTabs,
        grapesjsCustomCode,
        grapesjsTouch,
        grapesjsParserPostCss,
        grapesjsTooltip,
        grapesjsTyped,
        grapesjsStyleBg,
        grapesjsCodeEditor

    ],
    storageManager: false,
    canvas: {
        styles:[
            'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'
        ]
    },
    pluginsOpts: {
        'gjs-blocks-basic': { flexGrid: true },
        'grapesjs-tabs': {
            tabsBlock: { category: 'Extra' }
        },
        'grapesjs-typed': {
            block: {
                category: 'Extra',
                content: {
                    type: 'typed',
                    'type-speed': 40,
                    strings: [
                        'Text row one',
                        'Text row two',
                        'Text row three',
                    ],
                }
            }
        },
        'grapesjs-preset-webpage': {
            modalImportTitle: 'Import Template',
            modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
            modalImportContent: function(editor) {
                return editor.getHtml() + '<style>'+editor.getCss()+'</style>'
            },
        },
    },
});



const pn = editor.Panels;
const panelViews = pn.addPanel({
    id: "views"
});
panelViews.get("buttons").add([
    {
        attributes: {
            title: "Open Code"
        },
        className: "fa fa-file-code-o",
        command: "open-code",
        togglable: false, //do not close when button is clicked again
        id: "open-code"
    }
]);

window.editor = editor;
