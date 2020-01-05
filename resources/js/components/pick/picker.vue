<template>
  <div>
    <v-stage class="stage" ref="stage" :config="configKonva" @wheel="zoom" @click="mark" @contextmenu="contextmenu">
      <v-layer>
        <v-image :config="configImage" />
      </v-layer>
      <v-layer>
        <v-circle v-for="s in star" :key="s._rlnCoordinateX+':'+s._rlnCoordinateY" :config="{x:Number(s._rlnCoordinateX),y:Number(s._rlnCoordinateY),radius:50,fill: '',stroke:'red',strokeWidth:2}" />
      </v-layer>
    </v-stage>
    <div class="toolbox">
      <button @click="save"><i class="fa fa-save"/></button>
    </div>
  </div>
</template>

<script>
  export default {
    props:['img','star'],
    data () {
      return {
        configImage:{
          width: 400,
          height: 400,
          image: null
        },
        configKonva: {
          width: 400,
          height: 400,
          draggable: true,
          scale:{x:this.zoomVal,y:this.zoomVal}
        },
        configCircle: {
          x: 100,
          y: 100,
          radius: 70,
          fill: 'red',
          stroke: 'black',
          strokeWidth: 4
        },
        zoomVal:1
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
      img(val,oldval){
        this.configImage.image.src = val;
        this.configImage.width = this.configImage.image.width;
        this.configImage.height = this.configImage.image.height;
        this.$refs.stage.getStage().draw();
      }
    },
    methods:{
      zoom(e){
        e.evt.stopPropagation();
        e.evt.preventDefault();
        var rawZoomVal=this.zoomVal;
        this.zoomVal+= e.evt.wheelDeltaY<0?0.1:-0.1;
        if(this.zoomVal < 0.2){
          this.zoomVal = 0.1;
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
        return Object.prototype.toString.call(obj).replace(/^\[object (\S+)\]$/, '$1');
      },
      mark(e){
        e.evt.preventDefault();
        if(e.evt.button===2 && e.target.__proto__.className==="Circle"){
          this.star.splice(e.target.index,1);
        }else if(e.evt.button===0 && e.target.__proto__.className==="Image"){
          let disx = (e.evt.offsetX - e.currentTarget.x())/this.zoomVal;
          let disy = (e.evt.offsetY - e.currentTarget.y())/this.zoomVal;
          this.star.push({"_rlnCoordinateX":disx,"_rlnCoordinateY":disy})
        }
      },
      save(){
        this.$emit("save");
      }
    },
  }
</script>
<style scoped lang="scss">
  .stage{
    overflow: hidden;
  }
  .toolbox{
    display: flex;
    align-content: center;
    justify-content: center;
    color: #ccc;
    background-color: rgba(0,0,0,0.5);
    position: relative;
    height:30px;
    margin-top: -30px;
    width:40%;
    left: 30%;
    border-radius: 10px;
  }

  .toolbox button{
    background-color: rgba(0,0,0,0);
    border: none;
    width:30px;

    &:hover{
      cursor: pointer;
      background-color: rgba(255,255,255,0.5);
    }

    &:focus{
      outline:none;
    }

    &:active{
      color:white;
    }
  }
</style>
