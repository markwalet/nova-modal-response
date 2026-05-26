import ModalActionResponse from './components/ModalActionResponse'
import TextBlock from './components/TextBlock'
import DividerBlock from './components/DividerBlock'

Nova.booting(app => {
    app.component('modal-response', ModalActionResponse)
    app.component('modal-response-text-block', TextBlock)
    app.component('modal-response-divider-block', DividerBlock)
});
