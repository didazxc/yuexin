<template>
  <div>
    <v-stage ref="stage" class="stage" :config="configKonva" @wheel="zoom" @click="mark" @contextmenu="contextmenu">
      <v-layer ref="layerImg">
        <v-image :config="configImage" />
      </v-layer>
      <v-layer ref="layerCircles">
        <v-circle v-for="s in picks" v-if="s.AutopickFigureOfMerit>=threshold" :key="s.CoordinateX+':'+s.CoordinateY" :config="{x:Number(s.CoordinateX),y:Number(s.CoordinateY),radius:radius,fill: '',stroke:Number(s.auto)?'blue':'red',strokeWidth:2}" />
      </v-layer>
    </v-stage>
    <div class="toolbox">
      <div class="block">
        <span class="demonstration">阈值</span>
        <el-slider v-model="threshold" :max="thresholdMax" :step="0.01"/>
      </div>
      <div class="block">
        <span class="demonstration">圆圈半径</span>
        <el-input-number style="flex: none;margin-right:10px;" size="mini" v-model="radius" @change="radiusChange" :min="1" :max="100" label="圆圈半径"/>
        <el-button @click="hide" type="primary" size="mini">{{hidden?'SHOW':'HIDDEN'}}</el-button>
        <el-button @click="applyThreshold" type="warning" size="mini">APPLY THRESHOLD</el-button>
        <el-button @click="save" type="success" size="mini">SAVE</el-button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props:['img','picks'],
    data () {
      return {
        configImage:{
          width: 800,
          height: 500,
          image: null
        },
        configKonva: {
          width: 800,
          height: 500,
          draggable: true,
          scale:{x:this.zoomVal,y:this.zoomVal}
        },
        radius:40,
        hidden:false,
        zoomVal:1,
        threshold:0,
        thresholdMax:2
      }
    },
    created(){
      const image = new window.Image();
      image.src = this.img;
      this.configImage.image = image;
      this.configImage.width = image.width;
      this.configImage.height = image.height;
    },
    watch:{
      img(val){
        this.configImage.image.src = val;
        this.configImage.width = this.configImage.image.width;
        //if(this.zoomVal===1)this.zoomVal=this.configImage.height/this.configImage.image.height;
        this.configImage.height = this.configImage.image.height;
        setTimeout(()=>this.$refs.layerImg.getStage().draw(),20);
      }
    },
    methods:{
      zoom(e){
        e.evt.stopPropagation();
        e.evt.preventDefault();
        var rawZoomVal=this.zoomVal;
        this.zoomVal+= e.evt.wheelDeltaY<0?0.01:-0.01;
        if(this.zoomVal < 0.2){
          this.zoomVal = 0.2;
        }else if(this.zoomVal > 5.0){
          this.zoomVal=5.0;
        }
        var stage=this.$refs.stage.getStage();
        var position = stage.absolutePosition();
        var x=this.zoomVal/rawZoomVal*(position.x-e.evt.layerX)+e.evt.layerX;
        var y=this.zoomVal/rawZoomVal*(position.y-e.evt.layerY)+e.evt.layerY;
        stage.absolutePosition({x:x,y:y})
          .scale({x:this.zoomVal,y:this.zoomVal}).draw();
      },
      contextmenu(e){
        e.evt.preventDefault();
      },
      getType(obj){
        let type  = typeof obj;
        if(type !== "object"){
          return type;
        }
        return Object.prototype.toString.call(obj).replace(/^\[object (\S+)]$/, '$1');
      },
      mark(e){
        e.evt.preventDefault();
        if(e.evt.button===2 && e.target.__proto__.className==="Circle"){
          this.picks.splice(e.target.index,1);
        }else if(e.evt.button===0 && e.target.__proto__.className==="Image"){
          let disx = (e.evt.offsetX - e.currentTarget.x())/this.zoomVal;
          let disy = (e.evt.offsetY - e.currentTarget.y())/this.zoomVal;
          this.picks.push({"CoordinateX":disx,"CoordinateY":disy,"auto":0,"AutopickFigureOfMerit":100})
        }
        this.$emit("mark");
      },
      save(){
        this.$emit("save");
      },
      hide(){
        var layer = this.$refs.layerCircles.getStage();
        this.hidden=layer.visible();
        layer.visible(!this.hidden);
        layer.draw();
      },
      radiusChange(){
        this.$refs.layerCircles.getStage().draw();
      },
      applyThreshold(){
        var i=0;
        while(i<this.picks.length){
          if(this.picks[i].AutopickFigureOfMerit<this.threshold){this.picks.splice(i,1);}else{i++;}
        }
        this.$emit("mark");
      }
    },
  }
</script>
<style scoped>
  .stage{
    overflow: hidden;
  }
  .toolbox{
    margin-top:10px;
  }
  div.block{
    display: flex;
    align-items:center;
    width:90%;
    padding:0 5%;
  }
  .block .demonstration{
    width:80px;
  }
  .block>div{
    flex:auto;
  }

</style>
