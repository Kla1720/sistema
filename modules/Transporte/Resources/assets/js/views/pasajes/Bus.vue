<template>
    <div class="mt-2" :style="{overflowX:'auto'}">
        <div :id="idElement" class="bus">
            <div class="element" v-for="(asiento,index) in asientos" :key="'el-'+index"
            :id="'seat-'+index"
            :style="{left:asiento.left,top:asiento.top,cursor: drag ? 'move' : 'pointer'}"
            v-on:dblclick="dbClick(asiento)"
            >
                <div v-if="remove" class="remove-element" @click="onDelete(asiento,index)">
                    <el-tooltip class="item" effect="dark" content="Eliminar" placement="top-start">
                        <i class="fa fa-minus"></i>
                    </el-tooltip>

                </div>
                <!-- Asiento normal -->
                <template v-if="asiento.type == 'ss'">
                    <svg  @mousedown="childOnMouseDown($event,asiento,index)" id="60611b2ba670a" gc-seat-static="0" gc-seat-element-id="2" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="51px" height="38px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd;" viewBox="0 0 51 38" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 9.554 13.384 58.672 38.443">

                        <g id="Capa_x0020_1">
                            <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                            <path class="fil0 str0" :style="stateAsiento(asiento)"  d="M12 34c1,0 1,0 2,0l-2 0z"></path>
                            <path class="fil1 str0" :style="stateAsiento(asiento)" d="M12 4c-2,0 -3,0 -3,0 -5,2 -6,7 -6,15 0,8 2,14 7,15 0,0 1,0 2,0 1,-1 1,-4 0,-6 2,-9 2,-9 0,-18 1,-1 1,-4 0,-6z"></path>
                            <path class="fil0 str0" :style="stateAsiento(asiento)" d="M14 4c-1,0 -2,0 -2,0l2 0z"></path>
                            <path class="fil0 str0" :style="stateAsiento(asiento)" d="M49 19c0,-8 -1,-14 -3,-15 -3,-1 -10,0 -19,0 -4,0 -9,0 -13,0 1,1 1,4 0,6 2,9 2,10 0,18 1,2 1,5 0,6 4,0 8,0 13,0 8,0 17,1 19,0 2,-1 3,-7 3,-15z"></path>

                            <path class="fil2 str1" :style="stateAsiento(asiento,{isSeat:true})" d="M48 29c-3,0 -6,0 -10,0 -1,0 -2,0 -3,0 -12,0 -22,-1 -24,-1 -3,-1 -1,-4 -1,-9 0,-4 -2,-8 1,-9 4,-1 19,-1 24,-1 1,0 2,0 3,0 4,0 7,0 10,0 2,2 2,18 0,20z"></path>
                            <path class="fil3 str2" :style="stateAsiento(asiento,{isBelt:true})" d="M18 4c1,2 1,4 0,6 2,10 2,9 0,18 1,3 1,3 0,6l4 0c1,-1 1,-4 0,-5 2,-9 2,-11 0,-19 1,-2 1,-5 0,-6l-4 0z"></path>
                            <path class="fil4 str1" :style="stateAsiento(asiento)" d="M42 34l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                            <path class="fil4 str1" :style="stateAsiento(asiento)" d="M42 1l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                        </g>
                    </svg>
                    <span @mousedown="childOnMouseDown($event,asiento,index)">{{ asiento.numero_asiento }}</span>
                </template>

                <!-- Baño -->
                <template v-else-if="asiento.type == 'sb'">
                    <svg @mousedown="childOnMouseDown($event,asiento,index)" id="60611b2ba69a4" gc-seat-static="1" gc-seat-element-id="7" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="35px" viewBox="0 0 80 80" style="z-index: 100;enable-background:new 0 0 40 40;" xml:space="preserve">
                        <g>
                            <path style="fill:#010002;" d="M17.256,50.212v19.387c0,0.762-0.251,1.394-0.723,1.85c-0.471,0.476-1.087,0.703-1.843,0.703
                                c-0.764,0-1.416-0.228-1.921-0.703c-0.541-0.456-0.79-1.088-0.79-1.85V50.212H4.715l7.558-25.522h-1.3
                                c-0.829,2.861-1.527,5.162-2.074,6.988c-0.492,1.817-1.038,3.542-1.512,5.202c-0.539,1.639-0.901,2.677-0.937,3.031
                                c-0.329,1.083-1.046,1.608-2.17,1.608l-0.572-0.138c-1.048-0.388-1.559-1.066-1.559-2.032c0-0.264,0.037-0.481,0.143-0.699
                                c0.075-0.187,0.292-0.828,0.546-1.916c0.292-1.079,0.684-2.421,1.157-3.981c0.471-1.585,0.981-3.301,1.515-5.142
                                c0.546-1.851,1.046-3.632,1.66-5.349c0.362-1.315,1.157-2.514,2.387-3.55c1.225-1.074,2.499-1.563,3.829-1.563h8.891
                                c1.375,0,2.711,0.489,3.904,1.563c1.16,1.041,1.994,2.234,2.459,3.55c0.498,1.717,1.007,3.498,1.51,5.349
                                c0.515,1.841,1.056,3.557,1.491,5.142c0.461,1.561,0.865,2.902,1.149,3.981c0.288,1.088,0.474,1.729,0.575,1.916l0.142,0.572
                                c0,1.168-0.546,1.851-1.592,2.151l-0.533,0.146c-1.152,0-1.877-0.526-2.165-1.608c-0.08-0.354-0.404-1.393-0.831-3.031
                                c-0.505-1.66-1.02-3.384-1.597-5.202c-0.578-1.82-1.256-4.127-2.084-6.993H23.4l7.547,25.528h-7.148v19.387
                                c0,0.762-0.217,1.394-0.795,1.85c-0.51,0.476-1.124,0.703-1.877,0.703c-0.795,0-1.38-0.228-1.877-0.703
                                c-0.471-0.456-0.684-1.088-0.684-1.85V50.212H17.256z M17.971,15.553c1.522,0,2.827-0.5,3.839-1.543
                                c1.043-1.067,1.589-2.393,1.589-3.993c0-1.522-0.546-2.827-1.589-3.904c-1.007-1.108-2.317-1.66-3.839-1.66
                                c-1.621,0-2.965,0.552-4.083,1.66c-1.083,1.072-1.616,2.382-1.616,3.904c0,1.6,0.533,2.925,1.621,3.993
                                C15.012,15.053,16.355,15.553,17.971,15.553z M57.876,16.984c-1.947,0-3.615,0.72-5.023,2.11c-1.413,1.453-2.139,3.143-2.139,5.025
                                v17.399c0,0.652,0.223,1.242,0.726,1.719c0.466,0.471,1.046,0.684,1.703,0.684c0.756,0,1.336-0.213,1.802-0.684
                                c0.404-0.477,0.617-1.066,0.617-1.719V25.96h1.258v43.074c0,0.829,0.362,1.596,1.051,2.186c0.647,0.652,1.403,0.932,2.273,0.932
                                c0.942,0,1.698-0.279,2.314-0.932c0.652-0.59,0.948-1.356,0.948-2.186V44.065h1.408v24.969c0,0.829,0.326,1.596,0.938,2.186
                                c0.616,0.652,1.413,0.932,2.387,0.932c0.828,0,1.554-0.279,2.206-0.932c0.611-0.59,0.891-1.356,0.891-2.186V25.96h1.315v15.558
                                c0,0.652,0.207,1.242,0.642,1.719c0.518,0.471,1.118,0.684,1.771,0.684c0.766,0,1.346-0.213,1.781-0.684
                                c0.393-0.477,0.642-1.066,0.642-1.719V24.12c0-1.877-0.684-3.573-2.097-5.02c-1.378-1.396-3.029-2.11-5.055-2.11H57.876V16.984z
                                M64.089,15.553c1.518,0,2.858-0.5,3.936-1.543c1.077-1.072,1.626-2.397,1.626-3.993c0-1.522-0.554-2.827-1.631-3.904
                                c-1.082-1.108-2.418-1.66-3.931-1.66c-1.595,0-2.936,0.552-3.945,1.66c-1.077,1.077-1.584,2.382-1.584,3.904
                                c0,1.6,0.507,2.925,1.584,3.993C61.159,15.053,62.5,15.553,64.089,15.553z M44.822,0h-5.486v79.536h5.486V0z"></path>
                        </g>
                    </svg>
                </template>
                <!-- Televisión -->
                <template v-else-if="asiento.type == 'sv'">
                    <svg @mousedown="childOnMouseDown($event,asiento,index)" id="60611b2ba6894" gc-seat-static="1" gc-seat-element-id="3" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="60px" height="38px" version="1.1" style="z-index: 100;shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 60 38" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 9.554 13.384 58.672 38.443">
                        <metadata id="CorelCorpID_0Corel-Layer3"></metadata>
                        <line class="fil03 str03" x1="41" y1="3" x2="32" y2="10"></line>
                        <line class="fil03 str03" x1="17" y1="3" x2="27" y2="10"></line>
                        <path class="fil13 str13" d="M44 12l-1 0c-2,-2 -10,-2 -13,-2 -7,0 -12,1 -14,2 -1,0 -2,5 -2,11 0,5 1,10 2,11 1,1 7,1 14,1 7,0 13,0 13,-1 2,-1 3,-6 3,-11 0,-6 -1,-10 -2,-11z"></path>
                        <path class="fil23 str13" d="M40 15l0 0c-2,-1 -8,-1 -10,-1 -5,0 -9,0 -10,1 -1,0 -1,3 -1,8 0,4 0,7 1,8 0,0 5,1 10,1 5,0 9,0 10,-1 1,0 2,-4 2,-8 0,-4 -1,-8 -2,-8z"></path>
                        <line class="fil03 str23" x1="23" y1="19" x2="30" y2="19"></line>
                        <line class="fil03 str23" x1="27" y1="27" x2="27" y2="20"></line>
                        <line class="fil03 str23" x1="34" y1="27" x2="32" y2="19"></line>
                        <line class="fil03 str23" x1="34" y1="27" x2="38" y2="19"></line>
                    </svg>
                </template>
                <!-- Escaleras -->
                <template v-else-if="asiento.type == 'ses'">
                    <svg :style="{position:'relative',left:'11px',top:'10px'}" @mousedown="childOnMouseDown($event,asiento,index)" id="60611b2ba6920" gc-seat-static="1" gc-seat-element-id="4" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="38px" height="38px" version="1.1" style="z-index: 100;shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 38 38" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g id="Capa_x0020_1">
                            <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                            <rect class="fil05" x="3" y="6" width="4.99865" height="27.0028"></rect>
                            <rect class="fil05" x="12" y="6" width="4.99865" height="27.0028"></rect>
                            <rect class="fil05" x="21" y="6" width="4.99865" height="27.0028"></rect>
                            <rect class="fil05" x="30" y="6" width="4.99865" height="27.0028"></rect>
                        </g>
                    </svg>
                    <svg @mousedown="childOnMouseDown($event,asiento,index)" id="60611b2ba694c" gc-seat-static="1" gc-seat-element-id="6" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="38px" height="38px" version="1.1" style="z-index: 100;shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 38 38" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g id="Capa_x0020_1">
                            <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                            <rect class="fil07" transform="matrix(1.56185 -1.56185 0.577983 0.577983 5.99776 9.40493)" width="2.50065" height="36.5034"></rect>
                        </g>
                    </svg>
                    <svg :style="{position:'relative',left:'-31px',top:'-30px'}" @mousedown="childOnMouseDown($event,asiento,index)" id="60611b2ba6920" gc-seat-static="1" gc-seat-element-id="4" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="38px" height="38px" version="1.1" style="z-index: 100;shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 38 38" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g id="Capa_x0020_1">
                            <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                            <rect class="fil05" x="3" y="6" width="4.99865" height="27.0028"></rect>
                            <rect class="fil05" x="12" y="6" width="4.99865" height="27.0028"></rect>
                            <rect class="fil05" x="21" y="6" width="4.99865" height="27.0028"></rect>
                            <rect class="fil05" x="30" y="6" width="4.99865" height="27.0028"></rect>
                        </g>
                    </svg>
                </template>

            </div>

            <!-- Cuadrículas de guía -->
            <div  class="content-bus" >
                <template v-if="move">
                    <div v-for="cs in 5" class="content-seat" :key="cs">
                        <div v-for="s in 18" class="seat" :key="cs+'-'+s"></div>
                    </div>
                </template>

            </div>

            <!-- Parte de atras del autobus -->
            <div :style="{position:'absolute',left:'2%',top:'17%'}">
                <svg id="60611b2ba5fdf" gc-seat-static="1" gc-seat-element-id="11" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="50px" height="210px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 50 210" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="Capa_x0020_1">
                        <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                        <g id="_2938788358864">
                            <rect class="fil011 str011" transform="matrix(-1.03493E-013 1.29173 1.12271 8.99511E-014 32.4558 6.54055)" width="2.92936" height="1.29107"></rect>
                            <path class="fil011 str011" d="M9 29c-5,52 -5,104 0,155 1,12 10,22 23,22l0 0c0,0 0,0 0,0l0 -4 0 0 0 0c0,0 0,0 -1,0l0 0c0,0 0,0 0,0l0 0 0 0c-10,0 -18,-9 -19,-21 -4,-49 -5,-100 0,-149 2,-12 9,-22 19,-22l0 0c1,0 1,0 1,0l0 0 0 0 0 -3c0,0 0,0 0,0l0 0c-13,0 -22,9 -23,22z"></path>
                            <rect class="fil011 str011" transform="matrix(-1.03493E-013 1.29173 1.12271 8.99511E-014 32.4466 202.435)" width="2.92936" height="1.29107"></rect>
                            <path class="fil111 str111" d="M23 8c-9,3 -13,10 -17,20 -4,53 -3,105 0,157 3,9 8,16 17,19"></path>
                        </g>
                        <line class="fil111 str111" x1="34" y1="7" x2="51" y2="7"></line>
                        <line class="fil111 str111" x1="34" y1="206" x2="51" y2="206"></line>
                    </g>
                </svg>
            </div>
            <!-- Parte de enfrente -->
            <div :style="{position:'absolute',left:'calc(100% - 141px)',top:'17%'}">
                <svg id="60611b2ba6d1b" gc-seat-static="1" gc-seat-element-id="9" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="141px" height="209px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 141 209" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="Capa_x0020_1">
                    <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                        <path class="fil09 str09" d="M120 15c2,3 2,3 3,9 7,35 6,53 6,81 0,23 1,48 -5,80 -1,3 -2,8 -4,10 -4,6 -6,8 -13,8l0 -4c9,0 12,-6 13,-14 4,-32 4,-56 4,-80 0,-24 0,-49 -5,-81 -2,-8 -3,-12 -12,-13l0 -4c6,0 10,3 13,8z"></path>
                        <rect class="fil19 str09" transform="matrix(-8.36377E-014 3.01874 -1.57888 -3.99724E-014 106.784 7.03962)" width="1.43009" height="1.05701"></rect>
                        <rect class="fil19 str09" transform="matrix(-8.36377E-014 3.01874 -1.57888 -3.99724E-014 106.784 198.953)" width="1.43009" height="1.05701"></rect>
                        <line class="fil19 str09" x1="105" y1="203" x2="-1" y2="203"></line>
                        <path class="fil19 str09" d="M106 199l0 -66c0,0 1,-4 -3,-4 -3,0 -14,0 -14,0 0,0 -3,0 -3,-4 0,-4 0,-19 0,-19l38 0"></path>
                        <path class="fil19 str09" d="M105 11l-24 0c-9,4 -2,12 -1,17 0,3 3,20 0,37 -2,7 -3,13 -3,18 -1,6 -1,12 -1,19 0,0 15,5 16,-5 2,-26 4,-51 -3,-77l-12 0"></path>
                        <line class="fil19 str09" x1="90" y1="27" x2="79" y2="27"></line>
                        <line class="fil19 str09" x1="94" y1="66" x2="79" y2="66"></line>
                        <line class="fil19 str09" x1="93" y1="84" x2="77" y2="84"></line>
                        <ellipse class="fil29 str19" transform="matrix(-9.93232E-014 3.58488 -3.75023 -9.4944E-014 67.7808 46.2415)" rx="6" ry="4"></ellipse>
                        <line class="fil19 str09" x1="105" y1="7" x2="-1" y2="7"></line>
                        <ellipse class="fil19 str19" transform="matrix(-8.36377E-014 3.01874 -3.15798 -7.99501E-014 67.7514 46.0626)" rx="6" ry="4"></ellipse>
                        <circle class="fil19 str29" transform="matrix(-7.96057E-014 2.87322 -3.00377 -7.6046E-014 67.7133 46.1349)" r="1"></circle>
                        <circle class="fil19 str29" transform="matrix(-1.43551E-013 5.18121 -5.41663 -1.37132E-013 67.738 46.0875)" r="1"></circle>
                        <path class="fil19 str09" d="M67 27l0 11c0,0 -1,1 -2,1 -1,-1 -5,-7 -5,-7"></path>
                        <path class="fil19 str09" d="M67 65l0 -11c0,0 -1,-1 -2,-1 -1,1 -5,7 -5,7"></path>
                        <path class="fil19 str09" d="M57 37c6,7 7,12 0,18"></path>
                        <path class="fil19 str09" d="M80 41l-8 0c0,0 -1,0 -1,-2 0,-2 0,-11 0,-11"></path>
                        <path class="fil19 str09" d="M80 51l-8 0c0,0 -1,0 -1,2 0,2 0,11 0,11"></path>
                        <line class="fil19 str39" x1="85" y1="106" x2="47" y2="106"></line>
                        <path class="fil19 str49" d="M49 46c0,-6 0,-11 -2,-12 -3,-1 -10,0 -18,0 -7,0 -15,-1 -17,0 -4,1 -5,6 -5,12 0,7 2,11 6,12 3,0 8,0 16,0 8,0 15,0 18,0 2,-1 2,-6 2,-12z"></path>
                        <path class="fil19 str49" d="M48 54c-2,0 -5,0 -8,0 -1,0 -2,0 -3,0 -12,0 -21,0 -23,-1 -2,0 0,-3 0,-7 0,-3 -2,-6 0,-7 4,-1 18,-1 23,-1 1,0 2,0 2,0 4,0 7,0 10,0 0,6 0,10 -1,16z"></path>
                        <path class="fil19 str49" d="M15 34c1,1 1,4 0,5 2,7 2,7 0,14 1,2 1,4 0,5l2 0c1,-1 1,-3 0,-5 2,-6 2,-7 0,-14 1,-2 1,-4 0,-5l-2 0z"></path>
                        <path class="fil19 str49" d="M21 34c0,2 0,3 0,5 2,8 1,7 0,14 0,2 0,3 0,5l4 0c0,-1 0,-3 -1,-4 2,-7 2,-8 1,-15 0,-2 0,-4 0,-5l-4 0z"></path>
                        <path class="fil29 str49" d="M44 58l-17 0c0,0 0,0 0,0l0 2c0,0 0,0 0,0l17 0c0,0 0,0 0,0l0 -2c0,0 0,0 0,0z"></path>
                        <path class="fil29 str49" d="M43 32l-17 0c0,0 0,0 0,0l0 2c0,0 0,0 0,0l17 0c0,0 0,0 0,0l0 -2c0,0 0,0 0,0z"></path>
                    </g>
                </svg>
            </div>

        </div>
    </div>
</template>
<script>
export default {

    props:{
        seats:{
            type:Array,
            required:true,
            default:() => []
        },
        drag:{
            type:Boolean,
            default:false
        },
        remove:{
            type:Boolean,
            default:false
        }
    },
    created(){
        this.asientos = this.seats;
        console.log("desde adentro del carro")
        console.log(this.asientos)
    },
    watch:{
        seats(newVal){
            this.asientos = newVal;
        }
    },
    data(){
        return ({
            idElement:+ new Date(),
            initX:0,
            initY:0,
            firstX:0,
            firstY:0,
            asientos:[],
            selectAsiento:null,
            targetBus:null,
            target:null,
            move:false,
        });
    },
    mounted(){
        this.targetBus = document.getElementById(this.idElement);
        window.addEventListener('mouseup',this.onMouseUp,false);
    },
    methods:{

        stateAsiento(asiento,config={}){

            /** Manejo de los estados del asiento */
            if(asiento.estado_asiento_id == 1){//Disponible
                return {
                    fill:'#fff',
                    animation:'none'
                }
            }else if(asiento.estado_asiento_id == 2 ){ //Ocupado

                if(config.isBelt){ //Hay un path que se pinta de azul
                    return{
                        fill:'#00AEFF'
                    };
                }
                return {
                    fill:'#1b99a5',
                    animation:'none'
                }

            }else if(asiento.estado_asiento_id == 3){ //Reservado

                if(config.isSeat){
                    return {
                        fill:'#fff',
                        animation:'reservado 1s infinite'
                    }
                }

                return {
                    fill:'#fff',
                    animation:'none'
                }

            }else if(asiento.estado_asiento_id == 4){ //Seleccionado

                if(config.isSeat){
                    return {
                        fill:'#003c71',
                        animation:'reservado 1s infinite'
                    }
                }

                return {
                    fill:'#003c71',
                    animation:'none'
                }

            }
        },
        childOnMouseDown(evt,asiento,index){
            evt.stopPropagation();
            if(!this.drag) return;
            this.onMouseDown(evt,asiento,index)
        },
        dbClick(asiento){
            this.selectAsiento = asiento;
            this.$emit('dbclick',this.selectAsiento);
        },
        onMouseDown(e,asiento,index){
            e.preventDefault();
            this.target = document.getElementById('seat-'+index);
            this.initX = this.target.offsetLeft;
            this.initY = this.target.offsetTop;
            this.firstX = e.pageX;
            this.firstY = e.pageY;
            this.selectAsiento = asiento;
            this.targetBus.addEventListener('mousemove',this.onMouseMove,false);
        },

        onMouseUp(e){
            if(this.target){
                this.selectAsiento.top = this.target.offsetTop + 'px';
                this.selectAsiento.left = this.target.offsetLeft + 'px';
            }
            this.targetBus.removeEventListener('mousemove',this.onMouseMove,false);
            this.target = null;
            this.move = false;
            this.$emit('mouseup',{event:e,target:this.target,asiento:this.selectAsiento});
        },

        onMouseMove(e){
            this.move = true; //muestro la cuadrícula de guía

            //cálculo la nueva posición de mi elemento en el eje x
            let x =  this.initX+e.pageX-this.firstX;
            //cálculo la nueva posición de mi elemento en el eje y
            let y = this.initY+e.pageY-this.firstY;

            //seteo las nuevas posiciones
            this.target.style.left = x + 'px';
            this.target.style.top = y + 'px';
        },

        onDelete(asiento,index){
            this.$emit('onDelete',asiento,index);
        }

    }

}
</script>
<style >
.remove-element{
    position: absolute;
    width: 13px;
    height: 13px;
    background-color: red;
    border-radius: 50%;
    cursor: pointer;
    left: calc(100% - 5px);
    display: flex;
    justify-content: center;
    color: #ffff;
}
</style>
