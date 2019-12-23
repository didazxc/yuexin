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
        tableData:[
          {name:'aa.mrc',df:18000,astig:1000,fit:3.5,mark:'good',picks:600,path:'/home/test/May08_03.05.02.bin.mrc'},
          {name:'aa1.mrc',df:18000,astig:1000,fit:3.5,mark:'good',picks:600,path:''},
          {name:'aa2.mrc',df:18000,astig:1000,fit:3.5,mark:'bad',picks:600,path:''},
          {name:'aa3.mrc',df:18000,astig:1000,fit:3.5,mark:'good',picks:600,path:''}
        ],
      }
    },
    methods:{
      handleCurrentChange(val){
        projectAPI.getMrc(val.path).then(res=>{
          this.imgsrc = res.data;
        })
      }
    }
  }
</script>

<style scoped>

</style>
