<template>
    <div class='header'>
        <div class="header-aside-btn" @click='collapse'>
            <i class="fa fa-bars" :class="{'fa-rotate-90':!isAsideCollapse}"/>
        </div>
        <div class='header-title'>{{title}}</div>
        <div class="header-tools"><slot/></div>
    </div>
</template>

<script type="text/javascript">
    export default {
        props:['isAsideCollapse','title'],
        methods:{
            collapse(){
                this.$emit('update:isAsideCollapse',!this.isAsideCollapse);
            },
        }
    }
</script>


<style scoped>
    .header {
        height:32px;
        padding:16px;
        background-color: rgb(187,230,214);
        color: rgb(44,62,80);
        text-align: center;
        font-size: 16px;
        display: flex;
        align-items: center;
        vertical-align: center;
        justify-content:space-between;
    }
    .header-aside-btn{
        cursor: pointer;
    }
    .header-title{
        font-size: 28px;
    }
</style>