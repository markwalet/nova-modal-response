import ModalActionResponse from './components/ModalActionResponse'
import TextBlock from './components/TextBlock'
import DividerBlock from './components/DividerBlock'
import HtmlBlock from './components/HtmlBlock'
import HeadingBlock from './components/HeadingBlock'
import ListBlock from './components/ListBlock'
import BadgeBlock from './components/BadgeBlock'
import CodeBlock from './components/CodeBlock'
import JsonBlock from './components/JsonBlock'
import InlineBlock from './components/InlineBlock'
import LinkBlock from './components/LinkBlock'
import IconBlock from './components/IconBlock'
import CollapsibleBlock from './components/CollapsibleBlock'
import ActionBlock from './components/ActionBlock'
import FormBlock from './components/FormBlock'

Nova.booting(app => {
    app.component('modal-response', ModalActionResponse)
    app.component('modal-response-text-block', TextBlock)
    app.component('modal-response-divider-block', DividerBlock)
    app.component('modal-response-html-block', HtmlBlock)
    app.component('modal-response-heading-block', HeadingBlock)
    app.component('modal-response-list-block', ListBlock)
    app.component('modal-response-badge-block', BadgeBlock)
    app.component('modal-response-code-block', CodeBlock)
    app.component('modal-response-json-block', JsonBlock)
    app.component('modal-response-inline-block', InlineBlock)
    app.component('modal-response-link-block', LinkBlock)
    app.component('modal-response-icon-block', IconBlock)
    app.component('modal-response-collapsible-block', CollapsibleBlock)
    app.component('modal-response-action-block', ActionBlock)
    app.component('modal-response-form-block', FormBlock)
});
