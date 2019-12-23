<template>
  <el-row :gutter="10">
    <el-col :sm="24" :md="12">
      <el-card shadow="hover">
        <el-table :data="tableData" style="width: 100%" highlight-current-row @current-change="handleCurrentChange">
          <el-table-column label="#" width="30"><i slot-scope="scope" >{{scope.$index}}</i></el-table-column>
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
                <el-image/>
              </el-col>
              <el-col :sm="24" :md="12">
                <el-image/>
              </el-col>
            </el-row >
            <el-row :gutter="10">
              <el-col :sm="24" :md="12">
                <el-image/>
              </el-col>
              <el-col :sm="24" :md="12">
                <el-image/>
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
  export default {
    name: "Preprocess",
    data(){
      return {
        currentRow:null,
        tableData:[
          {name:'aa.mrc',df:18000,astig:1000,fit:3.5,mark:'good',},
          {name:'aa1.mrc',df:18000,astig:1000,fit:3.5,mark:'good'},
          {name:'aa2.mrc',df:18000,astig:1000,fit:3.5,mark:'bad'},
          {name:'aa3.mrc',df:18000,astig:1000,fit:3.5,mark:'good'}
        ],
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
        this.currentRow = val;
      },
      mark(val){
        this.currentRow.mark=val;
      }
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
  }
</style>
