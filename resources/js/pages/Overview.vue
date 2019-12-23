<template>
  <el-card shadow="hover">
    <el-table :data="files" style="width: 100%">
      <el-table-column>
        <template slot="header" slot-scope="scope">
          Movie
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="" />
      </el-table-column>
      <el-table-column>
        <template slot="header" slot-scope="scope">
          Motion-corr R
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="/MotionCor2" />
      </el-table-column>
      <el-table-column>
        <template slot="header" slot-scope="scope">
          CTF R
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="/Gctf" />
      </el-table-column>
      <el-table-column>
        <template slot="header" slot-scope="scope">
          Mark
        </template>
        <template slot-scope="scope">
          <el-tag
              :type="scope.row[''] === undefined || scope.row[''][0]['mark']==='good' ? 'success' : 'info'"
              disable-transitions>{{scope.row[''][0]['mark']}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column>
        <template slot="header" slot-scope="scope">
          Pick
        </template>
        <el-tag type="primary" disable-transitions>1000</el-tag>
      </el-table-column>
      <el-table-column>
        <template slot="header" slot-scope="scope">
          Extract R
        </template>
        <img />
      </el-table-column>
    </el-table>
    <div class="tool-box">
      <el-button type="primary" size="small">TEST</el-button>
      <el-button type="danger" size="small">RUN ALL</el-button>
      <el-button type="success" size="small">SAVE</el-button>
    </div>
  </el-card>
</template>

<script>
  import projectAPI from '../api/project';
  import imgInTableCell from '../components/overview/imgInTableCell';

  export default {
    name: "Overview",
    components:{
      imgInTableCell
    },
    data() {
      return {
        files:[],
        tableData: [{
          date: '2016-05-02',
          name: '王小虎',
          address: '/homt/test/May08_03.05.02.bin.mrc'
        }, {
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1517 弄'
        }, {
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1519 弄'
        }, {
          date: '2016-05-03',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1516 弄'
        }]
      }
    },
    computed:{
      dir(){
        return this.$store.getters.getProject.directory;
      }
    },
    methods:{
      handleEdit(a, b,c){
        console.log(a,b,c);
      }
    },
    mounted(){
      projectAPI.getFiles(this.dir,'mrc').then(res=>{
        this.files.splice(0,this.files.length);
        res.data.forEach((item,index,array)=>{this.files.push(item)});
      });
    }
  }
</script>

<style scoped>
  .tool-box{
    float: right;
    margin: 10px;
  }
</style>
