<template>
  <div>
    <div v-if="isDrawer && !collapse" class="modal" @click="handleCollapse"></div>
    <aside :class="{drawer:isDrawer,close: isDrawer && collapse}">
      <div class='aside-header' @click='handleCollapse'>
        <img src="https://element.eleme.cn/favicon.ico" alt="C">
        <span v-if='!(!isDrawer && this.collapse)'><b>Logo</b></span>
      </div>
      <el-menu 
      router
      class="el-menu-vertical-demo"
      :default-active="defaultActive"
      @select="handleSelect"
      :collapse="!isDrawer && this.collapse"
        text-color="#2c3e50"
        active-text-color="#42b983">

        <el-menu-item v-for="(v,k) in menus" :key="k" :index="k">
          <i class="fa" :class="[v.fa]"/>
          <span slot="title">{{v.title}}</span>
        </el-menu-item>

      </el-menu>
    </aside>
  </div>
</template>

<script>
  export default {
    props:['collapse','defaultActive','menus'],
    data(){
      return {
        isDrawer:false,
      }
    },
    methods: {
      handleCollapse(event){
        this.setAsideCollapse(!this.collapse);
      },
      setAsideCollapse(value){
        this.$emit('update:collapse',value);
      },
      handleSelect(key, keyPath){
        this.$emit('select',key);
      },
      resize(){
        if(document.body.clientWidth<768){
          //变成抽屉状态
          this.isDrawer=true;
          this.setAsideCollapse(true);
        }else if(document.body.clientWidth<1200){
          //md以下屏幕，默认合并状态
          this.isDrawer=false;
          this.setAsideCollapse(true);
        }else{
          this.isDrawer=false;
          this.setAsideCollapse(false);
        }
      }
    },
    mounted() {
      this.resize();
      window.addEventListener('resize', this.resize);
    },
  }
</script>

<style scoped>

  aside{
    height:100vh;
    background-color: #FFF;
    overflow: hidden;
    box-shadow:2px 0 10px rgba(0,0,0,.1);
    z-index:2010;
  }

  aside.drawer{
    position:absolute;
    transition: transform 0.5s ease;
  }

  aside.close{
    transform: translateX(-100%);
  }

  .modal{
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 100%;
    z-index:2001;
    background-color: rgba(0,0,0,0.5);
  }

  .fa{
    width: 24px;
    text-align: center;
  }

  .el-menu-vertical-demo{
    height: 100%;
  }
  .el-menu-vertical-demo:not(.el-menu--collapse) {
    width: 200px;
    min-height: 400px;
  }
  .el-menu-item.is-active{
    color: '#42b983' !important;
    background-color: rgba(66,185,131,0.05);
  }
  .el-menu-item:hover,.el-submenu__title:hover{
    background-color: rgba(66,185,131,0.05);
  }
  .aside-header{
    height:32px;
    padding:16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .aside-header>img{
    height:32px;
  }
  .aside-header>span{
    margin-left: 5px;
  }
  .progress{
    position: absolute;
    top: 0;
    left: 17px;
  }
</style>