<template>
<div ref="moveable" :style="getStyle" @click="e => e.stopPropagation()" @mousedown="moveable ? onMouseDown : null" :class="{'select-item':selected,'':!selected}" >
    
    <div class="point point-left" @mousedown="mouseDownResize($event,'point-left')"></div>
    <div class="point point-top" @mousedown="mouseDownResize($event,'point-top')"></div>
    <div class="point point-right" @mousedown="mouseDownResize($event,'point-right')"></div>
    <div class="point point-bottom" @mousedown="mouseDownResize($event,'point-bottom')"></div>
    
    <slot></slot>
</div>
    
</template>
<script>
export default {
    props:{
        initX:{
            type:String,
            default:'0'
        },
        initY:{
            type:String,
            default:'0'
        },
        moveable:false
    },
    created(){
        this.posX = this.initX;
        this.posY = this.initY;
    },
    computed:{
        getStyle(){
            return{
                position:'absolute',
                left:this.posX,
                top:this.posY,
                width:'100%',
                height:'100%'
            };

            
        }
    },
    data:() => ({
        positionX:0,
        posX:0,
        posY:0,
        positionY:0,
        firstX:0,
        firstY:0,
        selected:false,

        //
        px:0,
        py:0,
        fx:0,
        fy:0,
        point:null,
    }),
    mounted(){
        this.$nextTick(() => {
            let px = `${this.$refs.moveable.offsetLeft}px`
            let py = `${this.$refs.moveable.offsetTop}px`;
            this.posX = px;
            this.posY = py;
        });

        window.addEventListener('click',() => {
            this.selected = false;
        });

    },
    methods:{
       onMouseDown(e){
            e.stopPropagation();
            e.preventDefault();
            this.selected=true;
            this.positionX = this.$refs.moveable.offsetLeft;
            this.positionY = this.$refs.moveable.offsetTop;
            this.firstX = e.pageX;
            this.firstY = e.pageY;
            window.addEventListener('mousemove',this.onMouseMove,false);
            window.addEventListener('mouseup',this.onMouseUp,false);
            // this.targetBus.addEventListener('mousemove',this.onMouseMove,false);
        },
        onMouseUp(e){
            // if(this.target){
            //     this.selectAsiento.top = this.target.offsetTop + 'px';
            //     this.selectAsiento.left = this.target.offsetLeft + 'px';
            // }
            // this.selected=false;
            window.removeEventListener('mousemove',this.onMouseMove,false);
            window.onmouseup = null;
            this.$emit('mouseup',{event:e,target:this.target,asiento:this.selectAsiento});
        },

        onMouseMove(e){
           

            //cálculo la nueva posición de mi elemento en el eje x
            let x =  this.positionX+e.pageX-this.firstX;
            //cálculo la nueva posición de mi elemento en el eje y
            let y = this.positionY+e.pageY-this.firstY;

            //seteo las nuevas posiciones
            
            this.posX = `${x}px`;
            this.posY = `${y}px`;
        },


        mouseUp(){
            window.removeEventListener('mousemove',this.mouseMoveResize);
            window.onmouseup = null;
        },
        mouseDownResize(evt,point){
            evt.stopPropagation();
            this.px = this.$refs.moveable.offsetLeft;
            this.py = this.$refs.moveable.offsetTop;
            this.fx = evt.pageX;
            this.fy = evt.pageY;
            this.point = point;

            window.addEventListener('mousemove',this.mouseMoveResize);
            window.addEventListener('mouseup',this.mouseUp);


        },
        mouseMoveResize(evt){
            let px = this.$refs.moveable.offsetLeft;
            let py = this.$refs.moveable.offsetTop;
            let sizeWidth = this.$refs.moveable.offsetWidth;
            let sizeHeight = this.$refs.moveable.offsetHeight;

            //cálculo la nueva posición de mi elemento en el eje x
            let x =  this.fx - evt.pageX;
            //cálculo la nueva posición de mi elemento en el eje y
            let y = this.fy - evt.pageY;


            if(this.point == 'point-left'){
                this.$refs.moveable.style.width = `${sizeWidth + x}px`;
                this.posX = `${px + x}px`;
            }
            if(this.point == 'point-right'){
                this.$refs.moveable.style.width = `${sizeWidth - x}px`;
            }
            if(this.point == 'point-top'){
                this.$refs.moveable.style.height = `${sizeHeight + y}px`;
                this.posY = `${py - y}px`;
            }
            if(this.point == 'point-bottom'){
               this.$refs.moveable.style.height = `${sizeHeight - y}px`;
            }


            this.fx = evt.pageX;
            this.fy = evt.pageY;

            
        }
    }
    
}
</script>
<style scoped>
.select-item{
    border: 1px solid #5bc0de;
}

.point{
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: red;
    border-radius: 50%;
}

.point-left{
    top: 50%;
    left: -6px;

}

.point-top{
    left: 50%;
    top: -6px;
}

.point-right{
    top: 50%;
    right: -6px;

}
.point-bottom{
    bottom: -6px;
    left: 50%;

}
</style>