<template></template>
<script lang="ts">
//@ts-nocheck
import { h } from 'vue';
const initializeNodes = (props, emit, headText) => {
    const elementSanitizer = (e) => {
        let subnodeProps = {
            innerHTML: undefined,
            class: undefined,
        };
        let renderedSubNodes = [];
        if (e.subNodes !== undefined) {
            e.subNodes.forEach(_subnodes => {
                let generatedSubnode = elementSanitizer(_subnodes);
                renderedSubNodes.push(generatedSubnode);
                //has childs
                if (generatedSubnode.childs.length) {
                    subnodeProps.innerHTML = undefined;
                    subnodeProps.class = undefined;
                }

            });
        }
        if (e.innerHTML !== undefined) subnodeProps.innerHTML = e.innerHTML;
        if (e.class !== undefined) subnodeProps.class = e.class;
        if (e.title !== undefined) subnodeProps.title = e.title;

        if (e.trigger !== undefined) {
            let triggerData = e.trigger;
            if (triggerData.type == "click") {
                subnodeProps.onClick = function (event) {
                    let clickData = {
                        name: triggerData.emitName,
                        firstParam: triggerData.firstParam,
                        secondParam: triggerData.secondParam !== undefined ? triggerData.secondParam : null,
                        event: event
                    }
                    emit("triggeredevent", clickData)
                }
            }
        }

        if (subnodeProps.innerHTML === undefined) delete subnodeProps.innerHTML;
        if (subnodeProps.class === undefined) delete subnodeProps.class;


        return {
            type: e.elementType,
            attr: subnodeProps,
            childs: renderedSubNodes
        }

    }

    const cellSanitizer = (cell) => {
        if (cell === undefined) throw new Error('Column Not Exist!');
        if (cell.nodeActive === false) return undefined;
        let elementAttributes = {
            type: "td",
            attr: {
                'data-title': headText,
            },
            childs: []
        };
        let hasChildButNoData = false;
        if (cell.nodes !== undefined) {
            let isRaw = typeof cell.isRaw === "undefined" && cell.isRaw === true ? true : false;
            if (!isRaw) {
                let childs = cell.nodes.filter(e => e !== undefined && e?.nodeActive !== false).map(e => elementSanitizer(e));
                if (childs.length) elementAttributes.childs = childs;
                else hasChildButNoData = true;
            }
        }
        if (!elementAttributes.childs.length) elementAttributes.attr.innerHTML = cell.displayData;
        if (hasChildButNoData) elementAttributes.attr.innerHTML = "";

        return elementAttributes;
    }

    return cellSanitizer(props)
};

//const renderNodes = (data) => h(data.type, data.attr, data.childs.map(e => renderNodes(e)))
const renderNodes = (data) => h(data.type, data.attr, data.childs.filter(e => e !== undefined).map(e => renderNodes(e)))

export default {
    emits: ['triggeredevent'],
    props: ['cell', 'headText'],
    setup(props, { emit }) {
        return () => renderNodes(initializeNodes(props.cell, emit, props.headText))
    },
}
</script>

<style scoped></style>
