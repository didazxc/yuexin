<template>
  <el-row :gutter="10" @keydown.down="move(index+1)" @keydown.up="move(index-1)">
    <el-col :sm="24" :md="12" @keyup.down="move(index+1)" @keyup.up="move(index-1)">
      <el-card shadow="hover" @keyup.down="move(index+1)" @keyup.up="move(index-1)">
        <el-table ref="table" height="500" :data="tableData" style="width: 100%" highlight-current-row @current-change="handleCurrentChange">
          <el-table-column label="#" width="60"><i slot-scope="scope" >{{scope.$index}}</i></el-table-column>
          <el-table-column prop="name" label="Name"/>
          <el-table-column prop="df" label="DF"/>
          <el-table-column prop="fit" label="CTF fit"/>
          <el-table-column prop="astig" label="Astig"/>
          <el-table-column label="mark">
            <el-tag slot-scope="scope" disable-transitions :type="scope.row.mark === 'good' ? 'success' : 'info'">{{scope.row.mark}}</el-tag>
          </el-table-column>
        </el-table>
        <div class="tool-box">
          <el-button type="success" size="small" @click="mark('good')">MARK AS GOOD</el-button>
          <el-button type="danger" size="small" @click="mark('bad')">MARK AS BAD</el-button>
        </div>
      </el-card>
    </el-col>
    <el-col :sm="24" :md="12">
      <el-card shadow="hover">
        <el-tabs>
          <el-tab-pane label="预览">
            <el-row :gutter="10">
              <el-col :sm="24" :md="12">
                <el-image :src="src['Movies']" fit="contain"
                          :preview-src-list="[src['Movies']]">
                  <div slot="placeholder" class="image-slot">
                    加载中<span class="dot">...</span>
                  </div>
                </el-image>
              </el-col>
              <el-col :sm="24" :md="12">
                <el-image :src="src['trace']" fit="contain"
                          :preview-src-list="[src['trace']]">
                  <div slot="placeholder" class="image-slot">
                    加载中<span class="dot">...</span>
                  </div>
                </el-image>
              </el-col>
            </el-row >
            <el-row :gutter="10">
              <el-col :sm="24" :md="12">
                <el-image :src="src['MotionCor']" fit="contain"
                          :preview-src-list="[src['MotionCor']]">
                  <div slot="placeholder" class="image-slot">
                    加载中<span class="dot">...</span>
                  </div>
                </el-image>
              </el-col>
              <el-col :sm="24" :md="12">
                <el-image :src="src['CTF']" fit="contain"
                          :preview-src-list="[src['CTF']]">
                  <div slot="placeholder" class="image-slot">
                    加载中<span class="dot">...</span>
                  </div>
                </el-image>
              </el-col>
            </el-row>
          </el-tab-pane>
          <el-tab-pane label="统计">
            <v-chart :options="options"/>
          </el-tab-pane>
        </el-tabs>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
  import projectAPI from "../api/project";
  import mrcImg from "../components/common/mrcImg";

  export default {
    name: "Preprocess",
    components:{mrcImg},
    data(){
      return {
        currentRow:null,
        index:0,
        tableData:[],
        src:{'Movies':'','trace':'','MotionCor':'','CTF':''},
        options:{
          xAxis: {},
          yAxis: {},
          series: [{
            symbolSize: 20,
            data: [
              [10.0, 8.04],
              [8.0, 6.95],
              [13.0, 7.58],
              [9.0, 8.81],
              [11.0, 8.33],
              [14.0, 9.96],
              [6.0, 7.24],
              [4.0, 4.26],
              [12.0, 10.84],
              [7.0, 4.82],
              [5.0, 5.68]
            ],
            type: 'scatter'
          }]
        },
      }
    },
    methods:{
      handleCurrentChange(val){
        for(var i=0;i<this.tableData.length;i++){
          if(this.tableData[i]===val){
            this.index=i;
          }
        }
        this.currentRow = val;
        this.src['Movies']='';
        this.src['trace']='';
        this.src['MotionCor']='';
        this.src['CTF']='';
        projectAPI.getPng('Movies',val.name,'mrc').then(res=>{this.src['Movies']=res.data.data;});
        projectAPI.getPng('MotionCor',val.name,'aln').then(res=>{this.src['trace']=res.data.data;});
        projectAPI.getPng('MotionCor',val.name,'mrc').then(res=>{this.src['MotionCor']=res.data.data;});
        projectAPI.getPng('CTF',val.name,'ctf').then(res=>{this.src['CTF']=res.data.data;});
      },
      mark(val){
        //todo 提交
        this.currentRow.mark=val;
      },
      move(index){
        if(index>=0 && index<this.tableData.length) {
          this.index = index;
          this.$refs.table.setCurrentRow(this.tableData[index]);
        }
      }
    },
    mounted() {
      //更新tableData
      projectAPI.preprocess().then(res => {
        this.tableData.splice(0, this.tableData.length);
        res.data.data.forEach((item, index, array) => {
          this.tableData.push(item)
        });
        if(this.tableData.length){
          this.$refs.table.setCurrentRow(this.tableData[this.index]);
        }
      });
    }
  }
</script>

<style scoped>
  .tool-box{
    float: right;
    margin: 10px;
  }
  .el-image{
    height:100%;
    width:100%;
    border:#ccc solid 1px;
  }
</style>
