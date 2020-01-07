<template>
  <el-row :gutter="10">
    <el-col :sm="24" :md="12">
      <el-card shadow="hover">
        <el-table :data="tableData" style="width: 100%" highlight-current-row @current-change="handleCurrentChange">
          <el-table-column label="#" width="30"><i slot-scope="scope">{{scope.$index}}</i></el-table-column>
          <el-table-column prop="name" label="Name"/>
          <el-table-column prop="df" label="DF"/>
          <el-table-column prop="fit" label="CTF fit"/>
          <el-table-column prop="picks" label="Picks"/>
        </el-table>
      </el-card>
    </el-col>
    <el-col :sm="24" :md="12">
      <el-card shadow="hover">
        <picker :img="imgsrc" :star="star"/>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
  import projectAPI from "../api/project";
  import picker from "../components/pick/picker";
  export default {
    name: "Pick",
    components:{picker},
    data(){
      return {
        currentRow:null,
        imgsrc:'',
        star:[],
        tableData:[],
      }
    },
    methods:{
      handleCurrentChange(val){
        projectAPI.getPng('MotionCor',val.name,'mrc').then(res=>{
          this.imgsrc = res.data;
        });
        projectAPI.getMark(val.name).then(res=>{
          this.star = res.data;
        });
      }
    },
    mounted() {
      //更新tableData
      projectAPI.pick().then(res => {
        this.tableData.splice(0, this.tableData.length);
        res.data.forEach((item, index, array) => {
          this.tableData.push(item)
        });
      });
    }
  }
</script>

<style scoped>

</style>
