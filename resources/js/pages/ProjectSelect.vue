<template>

  <el-collapse accordion v-model="activeName" v-loading="loading">
    <el-collapse-item title="用户选择" name="1">
      <div class="project-select">
        <el-card v-for="user in users" :key="user.id" shadow="hover" :class="{'active':isActiveUser(user)}">
          <div class="inner-card" @click="selectUser(user)">{{user.name}}</div>
        </el-card>
      </div>
    </el-collapse-item>
    <el-collapse-item title="项目选择" name="2">
      <div class="project-select">
        <el-card v-for="project in activeUser.projects" :key="project.id" shadow="hover">
          <div class="inner-card" @click="selectProject(project)">{{project.name}}</div>
        </el-card>
        <el-card :shadow="activeUser.id!==logUser.id?'never':'hover'"
                 :class="{disabled:activeUser.id!==logUser.id}">
          <div class="inner-card" @click="formShow=activeUser.id===logUser.id"><i class="fa fa-plus-circle"/></div>
        </el-card>
      </div>
    </el-collapse-item>

    <el-dialog
        title="新建项目"
        width="80%"
        :visible.sync="formShow">
      <el-form ref="form" :model="form" label-width="120px">
        <el-form-item label="项目名称">
          <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="项目路径">
          <el-input v-model="form.directory"/>
        </el-form-item>
        <el-form-item label="Movie pixel size">
          <el-input v-model="form.map.pixel"/>
        </el-form-item>
        <el-form-item label="Voltage">
          <el-input v-model="form.map.voltage"/>
        </el-form-item>
        <el-form-item label="CS">
          <el-input v-model="form.map.cs"/>
        </el-form-item>
        <el-form-item label="Dose per frame">
          <el-input v-model="form.map.dose"/>
        </el-form-item>
        <el-form-item label="Diameter">
          <el-input v-model="form.map.diameter"/>
        </el-form-item>
        <el-form-item label="Box Size">
          <el-input v-model="form.map.boxsize"/>
        </el-form-item>
        <el-form-item label="导入配置">
          <el-select v-model="form.importProjectDir" clearable placeholder="将使用默认配置">
            <el-option
                v-for="project in allProjects" :key="project.id" :label="project.name" :value="project.directory">
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="formShow = false">取 消</el-button>
        <el-button type="primary" @click="createProject">确 定</el-button>
      </span>
    </el-dialog>

  </el-collapse>

</template>

<script>
  export default {
    name: "ProjectSelect",
    data() {
      return {
        timer:null,
        loading:false,
        activeName: '1',
        activeUser:{},
        form: {
          name: '',
          directory: '',
          ssd_directory:'',
          map:{
            pixel:'',
            voltage:'',
            cs:'',
            dose:'',
            diameter:'',
            boxsize:''
          },
          importProjectDir:''
        },
        formShow: false
      }
    },
    computed: {
      logUser() {
        return this.$store.getters.getUser;
      },
      users() {
        return this.$store.getters.getUsers;
      },
      allProjects(){
        var res=[];
        for(var id in this.users){
          if(this.users.hasOwnProperty(id)){
            res=res.concat(this.users[id].projects)
          }
        }
        return res;
      }
    },
    methods: {
      selectUser(user) {
        this.activeUser = user;
        this.activeName = '2';
      },
      isActiveUser(user) {
        return this.activeUser.id === user.id;
      },
      createProject(){
        this.loading=true;
        this.formShow=false;
        this.$store.dispatch('createProject',this.form).then(res=>{
          this.activeUser=this.users[this.activeUser.id];
          this.loading=false;
        }).catch(res=>{
          console.log(res);
          this.loading=false;
        });
      },
      selectProject(project){
        this.$store.commit('setProject',project);
        this.$router.push({path:'/overview'});
      },
      refreshUsers(){
        this.$store.dispatch('refreshUsers').then(res=>{
          this.activeUser = this.users[this.logUser.id];
        });
      }
    },
    created() {
      this.refreshUsers();
      this.timer=setInterval(this.refreshUsers,20000);
    },
    beforeDestroy() {
      clearInterval(this.timer);
    }
  }
</script>

<style scoped>
  .el-collapse {
    margin: 10px;
  }

  .project-select {
    display: flex;
    flex-wrap: wrap;
  }

  .el-card {
    cursor: pointer;
    background-color: #fcfcfc;
    margin: 10px;
  }

  .active {
    background-color: rgba(187, 230, 214, 0.5) !important;
  }

  .inner-card {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 80px;
    width: 100px;
  }
  .disabled{
    cursor: default;
    background:linear-gradient(140deg, transparent 49.5%, #ccc 40.5%, #ccc 50.5%, transparent 50.5%);
  }
</style>