<template>
  <div tabindex="0" @keydown.down="move(index+1)" @keydown.up="move(index-1)" style="height:100%;">
  <el-row :gutter="10">
    <el-col :sm="24" :md="12">
      <el-card shadow="hover">
        <el-table ref="table" height="500" :data="tableData" style="width: 100%" highlight-current-row @current-change="handleCurrentChange">
          <el-table-column label="#" width="60"><i slot-scope="scope">{{scope.$index}}</i></el-table-column>
          <el-table-column prop="name" label="Name"/>
          <el-table-column prop="df" label="DF"/>
          <el-table-column prop="fit" label="CTF fit"/>
          <el-table-column prop="picks" label="Picks"/>
        </el-table>
      </el-card>
    </el-col>
    <el-col :sm="24" :md="12">
      <el-card shadow="hover">
        <picker :img="imgsrc" :picks="picks" @save="savePicks" @mark="mark"/>
      </el-card>
    </el-col>
  </el-row>
  </div>
</template>

<script>
  import projectAPI from "../api/project";
  import picker from "../components/pick/picker";
  export default {
    name: "Pick",
    components:{picker},
    data(){
      return {
        index:0,
        imgsrc:'',
        picks:[],
        tableData:[],
      }
    },
    methods:{
      mark(){
        this.tableData[this.index]["picks"]=this.picks.length;
      },
      handleCurrentChange(val){
        this.tableData.forEach((value,index,arr)=>{
          if(value['name']===val['name'])this.index=index;
        });
        projectAPI.getPng('MotionCor',val.name,'mrc').then(res=>{
          this.imgsrc = res.data.data;
        });
        projectAPI.getPick(val.name).then(res=>{
          this.picks = res.data.data;
        });
      },
      move(index){
        if(index>=0 && index<this.tableData.length) {
          this.index = index;
          this.$refs.table.setCurrentRow(this.tableData[index]);
        }
      },
      refresh(){
        //更新tableData
        projectAPI.pick().then(res => {
          this.tableData.splice(0, this.tableData.length);
          res.data.data.forEach((item, index, array) => {
            this.tableData.push(item)
          });
          if(this.tableData.length){
            this.$refs.table.setCurrentRow(this.tableData[this.index]);
          }
        });
      },
      savePicks(){
        projectAPI.setPick(this.tableData[this.index]["name"],this.picks).then(res=>{
          this.$message({type:'success',message:'标记成功'});
        });
      }
    },
    mounted() {
      this.refresh();
    }
  }
</script>

<style scoped>
  div:focus {
    outline: none;
  }
</style>
