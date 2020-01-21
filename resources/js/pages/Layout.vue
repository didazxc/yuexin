<template>
  <el-container direction='vertical'>
    <el-container>
      <Aside :collapse.sync="isAsideCollapse" :menus="menus" :default-active="menuKey"/>
      <el-container id="content" direction='vertical'>
        <Header :isAsideCollapse.sync="isAsideCollapse" :title="title">
          <el-dropdown :hide-on-click="false" @command="handleCommand">
            <el-avatar> {{userName}}</el-avatar>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item icon="el-icon-folder" command="page2ProjectSelect">切换项目</el-dropdown-item>
              <el-dropdown-item icon="el-icon-switch-button" command="logout">登出</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </Header>
        <div id="main"><router-view/></div>
      </el-container>
    </el-container>
  </el-container>
</template>

<script>
  import Aside from '../components/layout/Aside'
  import Header from '../components/layout/Header'

  export default {
    name: "Layout",
    components: {
      Aside,
      Header
    },
    data() {
      return {
        menuKey:'/overview',
        title:'',
        isAsideCollapse: false,
      }
    },
    computed: {
      menus() {
        return this.$store.getters.getMenus;
      },
      userName() {
        var user=this.$store.getters.getUser;
        if(user!==null) return user.name;
        else return 'noLogin';
      },
    },
    methods: {
      handleCommand(cmd){
        switch(cmd){
          case 'logout':
            this.$store.dispatch('logout');
            break;
          case 'page2ProjectSelect':
            this.$router.push({path:'/set_project'});
            break;
        }
      },
      setMenuKey(value){
        this.menuKey=value;
        this.title=this.menus[value].title;
      }
    },
    watch:{
      '$route.path':function(newVal,oldVal){
        this.setMenuKey(newVal);
      }
    },
    mounted(){
      this.setMenuKey(this.$route.path);
    }
  }
</script>

<style>
  body {
    margin: 0;
  }
  #content{
    height: 100vh;
    overflow: hidden;
  }
  #main{
    height:100%;
    overflow:scroll;
    padding: 20px;
  }
  #main::-webkit-scrollbar {
    display: none;
  }
</style>
