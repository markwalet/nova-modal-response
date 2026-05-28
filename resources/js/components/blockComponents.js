import TextBlock from './TextBlock.vue'
import DividerBlock from './DividerBlock.vue'
import HtmlBlock from './HtmlBlock.vue'
import HeadingBlock from './HeadingBlock.vue'
import ListBlock from './ListBlock.vue'
import BadgeBlock from './BadgeBlock.vue'
import CodeBlock from './CodeBlock.vue'
import JsonBlock from './JsonBlock.vue'
import InlineBlock from './InlineBlock.vue'
import LinkBlock from './LinkBlock.vue'
import IconBlock from './IconBlock.vue'
import CollapsibleBlock from './CollapsibleBlock.vue'
import ActionBlock from './ActionBlock.vue'

// The single source of truth mapping a block `type` to the component that
// renders it. Both the stack (ModalActionResponse.vue) and the inline group
// (InlineBlock.vue) dispatch through this one map — one map, two sites.
export const blockComponents = {
    text: TextBlock,
    divider: DividerBlock,
    html: HtmlBlock,
    heading: HeadingBlock,
    list: ListBlock,
    badge: BadgeBlock,
    code: CodeBlock,
    json: JsonBlock,
    inline: InlineBlock,
    link: LinkBlock,
    icon: IconBlock,
    collapsible: CollapsibleBlock,
    action: ActionBlock,
}
