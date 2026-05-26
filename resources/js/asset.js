import ModalActionResponse from './components/ModalActionResponse'
import TextBlock from './components/TextBlock'
import DividerBlock from './components/DividerBlock'
import HtmlBlock from './components/HtmlBlock'
import HeadingBlock from './components/HeadingBlock'

Nova.booting(app => {
    app.component('modal-response', ModalActionResponse)
    app.component('modal-response-text-block', TextBlock)
    app.component('modal-response-divider-block', DividerBlock)
    app.component('modal-response-html-block', HtmlBlock)
    app.component('modal-response-heading-block', HeadingBlock)
});
