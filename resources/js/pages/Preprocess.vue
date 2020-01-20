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
            <el-button slot-scope="scope" size="mini" @click="mark(scope.row.mark === 'good'?'bad':'good')" :type="scope.row.mark === 'good' ? 'success' : 'info'" round plain>{{scope.row.mark}}</el-button>
          </el-table-column>
        </el-table>
        <div class="tool-box">
          <el-button type="success" size="small" @click="refresh">REFRESH</el-button>
          <el-button type="primary" size="small" @click="submitMark">SUBMIT MARK</el-button>
          <el-button type="warning" size="small" @click="markThreshold">APPLY THRESHOLD</el-button>
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
            <v-chart ref="echarts" :options="options" :autoresize="true"/>
            <div class="block">
              <span class="demonstration">Y轴</span>
              <el-select v-model="currentY" placeholder="请选择">
                <el-option
                    v-for="item in ['df','fit','astig','shift']"
                    :key="item"
                    :label="item"
                    :value="item">
                </el-option>
              </el-select>
            </div>
            <div class="block">
              <span class="demonstration">DF</span>
              <el-slider v-model="thresholds.df" :max="thresholdsMax.df"/>
            </div>
            <div class="block">
              <span class="demonstration">Fit</span>
              <el-slider v-model="thresholds.fit" :max="thresholdsMax.fit"/>
            </div>
            <div class="block">
              <span class="demonstration">Astig</span>
              <el-slider v-model="thresholds.astig" :max="thresholdsMax.astig"/>
            </div>
            <div class="block">
              <span class="demonstration">Shift</span>
              <el-slider v-model="thresholds.shift" :max="thresholdsMax.shift"/>
            </div>
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
      var thresholdsMax={
        df:30000,
        fit:20,
        astig:2000,
        shift:10
      };
      return {
        currentRow:null,
        index:0,
        tableData:[],
        src:{'Movies':'','trace':'','MotionCor':'','CTF':''},
        currentY:'df',
        thresholds:{
          df:0,
          fit:0,
          astig:0,
          shift:0
        },
        thresholdsMax:thresholdsMax,
        options:{
          xAxis: {},
          yAxis: {},
          visualMap:[
            {show:false,dimension:2,max:thresholdsMax.df,calculable:true,precision:0.1,inRange:{symbolSize: [10, 10]},outOfRange:{symbolSize: [10, 10],color:['rgba(0,0,0,.2)']}},
            {show:false,dimension:3,max:thresholdsMax.fit,calculable:true,precision:0.1,inRange:{symbolSize: [10, 10]},outOfRange:{symbolSize: [10, 10],color:['rgba(0,0,0,.2)']}},
            {show:false,dimension:4,max:thresholdsMax.astig,calculable:true,precision:0.1,inRange:{symbolSize: [10, 10]},outOfRange:{symbolSize: [10, 10],color:['rgba(0,0,0,.2)']}},
            {show:false,dimension:5,max:thresholdsMax.shift,calculable:true,precision:0.1,inRange:{symbolSize: [10, 10]},outOfRange:{symbolSize: [10, 10],color:['rgba(0,0,0,.2)']}}
          ],
          series: [{
            type: 'scatter',
            data: []
          }]
        }
      }
    },
    watch:{
      currentY(val){
        this.refreshEChartsData();
      },
      'thresholds.df'(val){
        this.$refs.echarts.dispatchAction({
          type:'selectDataRange',
          visualMapIndex:0,
          selected:[val,this.thresholdsMax.df]
        });
      },
      'thresholds.fit'(val){
        this.$refs.echarts.dispatchAction({
          type:'selectDataRange',
          visualMapIndex:1,
          selected:[val,this.thresholdsMax.fit]
        });
      },
      'thresholds.astig'(val){
        this.$refs.echarts.dispatchAction({
          type:'selectDataRange',
          visualMapIndex:2,
          selected:[val,this.thresholdsMax.astig]
        });
      },
      'thresholds.shift'(val){
        this.$refs.echarts.dispatchAction({
          type:'selectDataRange',
          visualMapIndex:3,
          selected:[val,this.thresholdsMax.shift]
        });
      },
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
        this.currentRow.mark=val;
      },
      move(index){
        if(index>=0 && index<this.tableData.length) {
          this.index = index;
          this.$refs.table.setCurrentRow(this.tableData[index]);
        }
      },
      refreshEChartsData(){
        this.options.series[0].data.splice(0, this.options.series[0].data.length);
        this.tableData.forEach((item, index, array) => {
          this.options.series[0].data.push([index,item[this.currentY],item['df'],item['fit'],item['astig'],item['shift']]);
        });
      },
      markThreshold(){
        this.tableData.forEach((item, index, array) => {
          if(item['df']<this.thresholds.df ||
            item['fit']<this.thresholds.fit ||
            item['astig']<this.thresholds.astig ||
            item['shift']<this.thresholds.shift){
            item.mark='bad';
          }else{
            item.mark='good';
          }
        });
      },
      refresh(){
        //更新tableData
        projectAPI.preprocess().then(res => {
          this.tableData.splice(0, this.tableData.length);
          res.data.data.forEach((item, index, array) => {
            item['df']=parseFloat(item['df']);
            item['fit']=parseFloat(item['fit']);
            item['astig']=parseFloat(item['astig']);
            item['shift']=parseFloat(item['shift']);
            this.tableData.push(item);
          });
          this.refreshEChartsData();
          if(this.tableData.length){
            this.$refs.table.setCurrentRow(this.tableData[this.index]);
          }
        });
      },
      submitMark(){
        projectAPI.setMark(this.tableData).then(res=>{
          this.$message({
            type: 'success',
            message: '提交Mark成功'
          });
        });
      }
    },
    mounted() {
      this.refresh();
    }
  }
</script>

<style scoped>
  .tool-box{
    float: right;
    margin: 10px;
  }
  .el-image{
    border:#ccc solid 1px;
  }
  div.block{
    display: flex;
    align-items:center;
    width:90%;
    padding:0 5%;
  }
  .block .demonstration{
    width:60px;
  }
  .block>div{
    flex:auto;
  }
  .echarts {
    width: 100%;
    height: 100%;
    min-height:350px;
    min-width:300px;
  }
</style>
