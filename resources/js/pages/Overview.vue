<template>
  <el-card shadow="hover">
    <el-table height="500" :data="files" style="width: 100%" :header-cell-style="{'text-align':'center'}"
              :cell-style="{'text-align':'center'}">
      <el-table-column>
        <template slot="header">
          Movie
        </template>
        <imgInTableCell slot-scope="scope" v-if="scope.$index<5" :name="scope.row.name" :status="scope.row['Movies']?'加载中...':scope.row.name" module="Movies"/>
        <span v-else>{{scope.row.name}}</span>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          <el-checkbox v-model="MotionCorChecked">
          <el-button @click="openConfForm('MotionCor')">Motion-corr R</el-button>
          </el-checkbox>
        </template>
        <imgInTableCell slot-scope="scope" v-if="scope.$index<5" :name="scope.row.name" :status="scope.row['MotionCor']===null?'未开始':scope.row['MotionCor']" module="MotionCor"/>
        <span v-else>{{scope.row['MotionCor']===null?'未开始':scope.row['MotionCor']}}</span>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          <el-checkbox v-model="CTFChecked">
          <el-button @click="openConfForm('CTF')">CTF R</el-button>
          </el-checkbox>
        </template>
        <imgInTableCell slot-scope="scope" v-if="scope.$index<5" :name="scope.row.name" :status="scope.row['CTF']===null?'未开始':scope.row['CTF']" ext="ctf" module="CTF"/>
        <span v-else>{{scope.row['CTF']===null?'未开始':scope.row['CTF']}}</span>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          Mark
        </template>
        <template slot-scope="scope">
          <el-tag
              :type="scope.row['Mark']==='good' ? 'success' : 'info'"
              disable-transitions>{{scope.row['Mark']}}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          <el-checkbox v-model="PickChecked">
          <el-button @click="openConfForm('Pick')">Pick</el-button>
          </el-checkbox>
        </template>
        <el-tag type="primary" disable-transitions>1000</el-tag>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          Extract R
        </template>
        <imgInTableCell slot-scope="scope" v-if="scope.$index<5" :name="scope.row.name" :status="scope.row['Extract']" ext="ctf" module="CTF"/>
        <span v-else>{{scope.row['Extract']}}</span>
      </el-table-column>
    </el-table>
    <div class="tool-box">
      <el-button type="primary" size="small" @click="test" v-loading="testLoading">TEST</el-button>
      <el-button type="success" size="small">RUN ALL</el-button>
      <el-button type="danger" size="small" @click="clear" v-loading="clearLoading">CLEAR</el-button>
    </div>
    <el-dialog title="参数设置" :visible.sync="dialogVisible" width="80%">
      <confView :forms="forms[step]" :module.sync="forms['_current'][step]"/>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="setConfig">确 定</el-button>
      </span>
    </el-dialog>
  </el-card>
</template>

<script>
  import projectAPI from '../api/project';
  import imgInTableCell from '../components/overview/imgInTableCell';
  import confView from "../components/overview/confView";

  export default {
    name: "Overview",
    components: {
      imgInTableCell,
      confView
    },
    data() {
      return {
        timer:null,
        MotionCorChecked:true,
        CTFChecked:true,
        PickChecked:true,
        files: [],
        dialogVisible: false,
        forms:JSON.parse(JSON.stringify(this.$store.getters.getConfig)),
        step:'',
        testLoading:false,
        clearLoading:false,
      }
    },
    computed:{
      modules(){
        var modules=['MotionCor','CTF','Pick'];
        var step=0;
        if(this.PickChecked){
          step=3;
        }else if(this.CTFChecked){
          step=2;
        }else if(this.MotionCorChecked){
          step=1;
        }
        return modules.slice(0,step);
      }
    },
    watch:{
      MotionCorChecked(val){
        if(!val){
          this.CTFChecked=val;
          this.PickChecked=val;
        }
      },
      CTFChecked(val){
        if(val){
          this.MotionCorChecked=val;
        }else{
          this.PickChecked=val;
        }
      },
      PickChecked(val){
        if(val){
          this.MotionCorChecked=val;
          this.CTFChecked=val;
        }
      }
    },
    methods: {
      updateFiles(){
        projectAPI.overview().then(res => {
          this.files.splice(0, this.files.length);
          res.data.data.forEach((item, index, array) => {
            this.files.push(item)
          });
        });
      },
      openConfForm(step) {
        this.step=step;
        this.dialogVisible=true;
      },
      setConfig(){
        this.dialogVisible = false;
        this.$store.dispatch("setConfig",this.forms).then(res=>{
          this.$message.success("配置成功～");
        }).catch(res=>{
          this.$message.error("配置信息设置失败，请尝试重新提交");
        });
      },
      test(){
        if(this.files.length>0){
          this.testLoading=true;
          var names=this.files.slice(0,20).map(f=>f['name']);
          projectAPI.test(this.modules,names).then(res=>{
            this.testLoading=false;
          }).catch(res=>{
            this.testLoading=false;
          });
          this.updateFiles();
        }
      },
      clear(){
        this.clearLoading=true;
        this.$confirm('此操作将永久删除文件, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(()=>{
          projectAPI.clear().then(res=>{
            this.$message({
              type: 'success',
              message: '删除成功!'
            });
            this.updateFiles();
            this.clearLoading=false;
          }).catch(res=>{
            this.clearLoading=false;
          });
        }).catch(()=>{
          this.clearLoading=false;
        });
      }
    },
    mounted() {
      this.updateFiles();
      //20s更新一次files
      this.timer=setInterval(this.updateFiles,10000);
    },
    beforeDestroy() {
      clearInterval(this.timer);
    }
  }
</script>

<style scoped>
  .tool-box {
    float: right;
    margin: 10px;
  }
</style>
