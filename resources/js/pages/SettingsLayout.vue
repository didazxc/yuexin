<template>
  <el-container direction='vertical'>
    <el-container>
      <Aside :collapse.sync="isAsideCollapse" :menus="menus" :default-active="menuKey" @select="changeMenu"/>
      <el-container direction='vertical'>
        <Header :isAsideCollapse.sync="isAsideCollapse" :title="title">
          <el-dropdown :hide-on-click="false" @command="handleCommand">
            <el-avatar> {{userName}}</el-avatar>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item icon="el-icon-switch-button" command="logout">登出</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </Header>
        <router-view/>
      </el-container>
    </el-container>
    <Login/>
  </el-container>
</template>

<script>
  import Aside from '../components/layout/Aside'
  import Header from '../components/layout/Header'

  export default {
    name: "SettingsLayout",
    components: {
      Aside,
      Header,
      Login
    },
    data() {
      return {
        menuKey: "/overview",
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
      title() {
        return this.menus[this.menuKey].title;
      }
    },
    methods: {
      changeMenu(key) {
        this.menuKey = key;
      },
      handleCommand(cmd){
        switch(cmd){
          case 'logout':
            this.$store.dispatch('logout');
            break;
        }
      }
    }
  }
</script>

<style>
  body {
    margin: 0;
  }

  body > .el-container {
    height: 100vh;
  }
</style>
