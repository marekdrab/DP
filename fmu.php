<?php
$results['id'] = 'BouncingBall';
$results['modelMode'] = 'continuous';
$results['stopTime'] = 4;
$results['dataSets'] = ["h","e"];
$results['stepSize'] = 0.001;
$results['interval'] = 20;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation interface</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/slider.css">
</head>
<body>

<main>
    <div class="container-fluid" id="main-container">
        <div class="d-flex justify-content-between mt-3">
            <div class="col" style="margin: 0 10px 0 10px;">
                <button class="btn btn-outline-primary btn-lg" id="play-btn-id"></button>
                <button class="btn btn-outline-primary btn-lg" id="pause-btn-id"></button>
                <button class="btn btn-outline-primary btn-lg" id="clear-btn-id"></button>
                <button class="btn btn-outline-primary btn-lg" id="values-reset-btn-id"></button>
            </div>
            <div class="mt-3 col-2">
                <label for="h0">Počiatočná výška</label>
                <input type="number" class="form-control init-value" placeholder="h" aria-label="h" id="h0" value="">
                <div class="slidecontainer">
                    <label for="range_id"id="range_label_id">e</label>
                    <input type="range" id="range_id" class="custom-range init-value"/>
                </div>
            </div>
        </div>
        <div id='spinner-blur' class="mt-3">
            <div class="flexblocks-item">
                <div id="chart-id"></div>
            </div>
        </div>
    </div>
</main>

<!--<div class="mt-3">-->
<!--<input type="text" class="form-control init-value" placeholder="h0" aria-label="h0" id="h0" value="4">-->
<!--</div>
        <div id='spinner-blur' class="mt-3">

            <div class="slidecontainer">-->
<!--<input type="range" id="range_id" class="custom-range"/>
                <label id="range_label_id" class="c3279"></label>-->
<!--</div>
            <div class="flexblocks-item">
                <div id="model-id"></div>
                <div id="chart-id"></div>
            </div>
        </div>-->

<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/createjs.min.js"></script>
<script src="js/plotly-latest.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootbox.all.min.js"></script>

<script src="fmu.js"></script>

<script>

    var dataFromXml = [];
    let id = <?php echo json_encode($results['id']) ?>;
    //let retVal = <?php //echo json_encode($retVal) ?>//;
    let dataSets = <?php echo json_encode($results['dataSets']) ?>;

    let stopTime = <?php echo json_encode($results['stopTime']) ?>;
    let modelMode = <?php echo json_encode($results['modelMode']) ?>;
    let stepSize = <?php echo json_encode($results['stepSize']) ?>;
    let interval = <?php echo json_encode($results['interval']) ?>;

    $(document).ready(function(){
        dataFromXml = parseModelDescription("models/" + id + "/modelDescription.xml",false);

        var htmlVars = "";
        var htmlParams = "";
        var parameters = {};
        var variables = {};
        var arrays = {};
        var datasets = {};
        $.each(dataFromXml.variables,function(i,item){
            variables[item.name] = {
                name: item.name,
                reference: item.reference,
                description: item.description,
                causality: item.causality,
                variability: item.variability,
                initial: item.initial,
                canHandleMultipleSetPerTimeInstant: item.canHandleMultipleSetPerTimeInstant
            };
        });

        $.each(dataFromXml.parameters,function(i,item){
            parameters[item.name] = {
                name: item.name,
                reference: item.reference,
                description: item.description,
                causality: item.causality,
                variability: item.variability,
                initial: item.initial,
                canHandleMultipleSetPerTimeInstant: item.canHandleMultipleSetPerTimeInstant
            };
        });

        $.each(dataFromXml.arrays,function(i,item){
            arrays[item.cname]={
                cname: item.cname,
                providers: item.providers
            }
        });

        $.each(dataSets,function(i,item){
            datasets['dataset-' + i]={
                id: 'dataset-' + i,
                name: item,
                mode: 'lines',
                fill: 'none',
                other: {},
                showlegend: true,
                xaxis: 'xaxis',
                yaxis: 'yaxis',
                offset: {
                    typeof: 'number',
                    value: 0,
                    complex: false,
                    provider: null,
                    function: value => value
                },
                line: {
                    color: '#'+(0x1000000+(Math.random())*0xffffff).toString(16).substr(1,6),
                    width: 2,
                    shape: 'linear',
                    smoothing: 1,
                    dash: 'solid',
                    simplify: true
                },
                x: {
                    typeof: 'number',
                    value: 0,
                    time: true,
                    provider: null,
                    array: false,
                    indexes: null,
                    function: null
                },
                y: {
                    typeof: 'number',
                    value: 0,
                    time: false,
                    provider: '{"type":"MV","id":"' + item +'","parent":"model-id"}',
                    array: false,
                    indexes: null,
                    function: null
                },
                maxSamples: {
                    typeof: 'number',
                    value: -1,
                    complex: false,
                    provider: null,
                    array: false,
                    indexes: null,
                    function: value => value
                }
            }
        });






        const mainJS = () => {
            const widgets = []
            const config = { widgets: {} }
            const WidgetType = {
                'MODEL': 'MODEL',
                'ANIMATE': 'ANIMATE',
                'ANIMATE_ANIM': 'ANIMATE_ANIM',
                'ANIMATE_TEXT': 'ANIMATE_TEXT',
                'RANGE': 'RANGE',
                'BUTTON': 'BUTTON',
                'ACTION': 'ACTION',
                'TOGGLE': 'TOGGLE',
                'CHART': 'CHART',
                'LABEL': 'LABEL',
                'CSS': 'CSS',
                'JAVASCRIPT': 'JAVASCRIPT'
            }
            const ProviderType = {
                'MODEL_PARAMETER': 'MP',
                'MODEL_VARIABLE': 'MV'
            }
            class Widget {
                constructor(configuration, typeIdentifier) {
                    Object.assign(this, configuration);
                    this.typeIdentifier = typeIdentifier;
                    this.listeners = {};
                    console.log('Widget ('.concat(this.typeIdentifier, ') instance ').concat(this.name));
                    this.component = this.locateComponent();
                    this.valueProviders = [];
                    this.generateSetters();
                    this.fillValueProviders();
                    this.updateComponent();
                    this.loadEventListeners();
                    this.constructed = true;
                }
                addValueProvider(attribute, provider) {
                    if (provider !== null) {
                        this.valueProviders[attribute] = provider;
                    }
                }
                fillValueProviders() {
                    Object.entries(this).forEach(([id, entry]) => {
                        if (entry == null || typeof entry !== 'object') {
                            return;
                        }
                        if (typeof entry['provider'] !== 'undefined' && entry.provider !== null) {
                            this.addValueProvider(id, entry.provider);
                        }
                    });
                }
                getValueProviders() {
                    return this.valueProviders;
                }
                setValueProvider(attribute, id, target) {
                    target.registerValueListener(this, id, attribute);
                    target.registerInitialValueListener(this, id, attribute);
                }
                setValueProviders(providers) {
                    Object.entries(providers).forEach(([attribute, config]) => {
                        this.setValueProvider(attribute, config.id, config.target);
                    });
                }
                locateComponent() {
                    const component = document.getElementById(this.id);
                    if (component === null || component === undefined) {
                        throw new ReferenceError('Widget ('.concat(this.name, ') of type (').concat(this.typeIdentifier, ') was not found.'));
                    }
                    return component;
                }
                generateSetters() {
                    console.warn('generateSetters for '.concat(this.name, ' is not overridden'));
                }
                updateComponent() {
                    Object.entries(this.setters).forEach(([name, setter]) => {
                        setter();
                    });
                }
                setValue(attribute, value, time) {
                    this[attribute].value = value;
                    this.setters[attribute]();
                }
                setArray(attribute, array, time) {
                    console.warn('setArray for '.concat(this.name, ' is not overridden'));
                }
                setValues(attribute, values, time) {
                    this.setValue(attribute, values[values.length - 1], time[time.length - 1]);
                }
                setArrays(attribute, arrays, time) {
                    console.warn('setArrays for '.concat(this.name, ' is not overridden'));
                }
                loadEventListeners() {
                    if (this.actions === undefined || this.actions === null) {
                        return;
                    }
                    Object.entries(this.actions).forEach(([key, action]) => {
                        this.addEventListener(action.event, action.function.bind(this));
                    });
                }
                addEventListener(type, callback) {
                    if (!(type in this.listeners)) {
                        this.listeners[type] = [];
                    }
                    this.listeners[type].push(callback);
                }
                removeEventListener(type, callback) {
                    if (!(type in this.listeners)) {
                        return;
                    }
                    let stack = this.listeners[type];
                    for (let i = 0, l = stack.length; i < l; i++) {
                        if (stack[i] === callback) {
                            stack.splice(i, 1);
                            return;
                        }
                    }
                }
                dispatchEvent(event) {
                    if (!(event.type in this.listeners)) {
                        return true;
                    }
                    let stack = this.listeners[event.type].slice();
                    for (let i = 0, l = stack.length; i < l; i++) {
                        stack[i].call(this, event);
                    }
                    return !event.defaultPrevented;
                }
            }
            ;
            config.actions = {
                'pause-action-id': function(model) {
                    model.pause();
                },
                'play-action-id': function(model) {
                    model.play();
                },
                'settimeout-action-id': function() {
                    window.setTimeout(this.updateComponent(), 50);
                },
                'initial-reset-action-id': function(model) {
                    model.resetWithInitial(true,true);
                },
                'new-values-reset-action-id':function(model){
                    $('input.init-value').each(function(){
                        model.setInitialValueByName($(this).attr('id'), parseFloat($(this).val()));
                    });
                    model.reset(true, true);
                },
                'graf-clear-action-id': function(graf) {
                    graf.clear();
                }
            }
            ;

            const animates = {}

            class AnimateRuntime {
                constructor(name, source, id) {
                    this.contents = {};
                    source(createjs, this.contents);
                    this.id = id;
                    const compositionIds = Object.keys(this.contents.compositions);
                    this.composition = this.contents.compositions[compositionIds[0]];
                    this.library = this.composition.getLibrary();
                    createjs.MotionGuidePlugin.install();
                    this.name = name;
                    if (this.library[this.name] === undefined) {
                        throw new Error('\''.concat(this.name, '\' is not a valid animate component'));
                    }
                    this.handleTick = this.handleTick.bind(this);
                    this.attachExportedComponents(this.library);
                    this.initialized = false;
                }
                init(canvas, autoplay = false) {
                    this.canvas = canvas;
                    this.canvas.style.display = 'block';
                    this.root = new this.library[this.name]();
                    this.stage = new this.library.Stage(this.canvas);
                    this.contents.compositionLoaded(this.library.properties.id);
                    this.components = this.filterExportedComponents(this.exportedComponents);
                    delete this.exportedComponents;
                    this.stage.setAutoPlay(autoplay);
                    this.stage.update();
                    this.stage.addChild(this.root);
                    this.resize = this.resize.bind(this);
                    this.startListeners();
                    this.initialized = true;
                    return new Promise(resolve => {
                        let tickCounter = 0;
                        const waitTicks = () => {
                            if (++tickCounter === 2) {
                                createjs.Ticker.removeEventListener('tick', waitTicks);
                                resolve();
                            }
                        };
                        createjs.Ticker.addEventListener('tick', waitTicks);
                    });
                }
                getComponents() {
                    if (typeof this.components !== 'undefined') {
                        return this.components;
                    }
                    return [];
                }
                destroy() {
                    if (this.initialized) {
                        this.stopListeners();
                        this.stage.clear();
                        this.deregisterClickHandlers();
                    }
                }
                attachCanvas(canvas) {
                    this.stopListeners();
                    this.stage.enableDOMEvents(false);
                    this.stage.canvas = canvas;
                    this.canvas = canvas;
                    this.stage.enableDOMEvents(true);
                    this.startListeners();
                }
                detachCanvas() {
                    this.stopListeners();
                    this.stage.enableDOMEvents(false);
                    this.stage.canvas = null;
                    this.canvas = null;
                    this.stage.enableDOMEvents(true);
                }
                startListeners() {
                    createjs.Ticker.addEventListener('tick', this.handleTick);
                }
                stopListeners() {
                    createjs.Ticker.removeEventListener('tick', this.handleTick);
                }
                handleTick() {
                    if (this.contents.Layer !== undefined) {
                        this.ZDepthHandleTick();
                    }
                    this.stage.update();
                    this.resize();
                }
                ZDepthHandleTick() {
                    const cameraInstance = this.root.___camera___instance;
                    if (cameraInstance !== undefined && cameraInstance.pinToObject !== undefined) {
                        cameraInstance.x = cameraInstance.pinToObject.x + cameraInstance.pinToObject.pinOffsetX;
                        cameraInstance.y = cameraInstance.pinToObject.y + cameraInstance.pinToObject.pinOffsetY;
                        if (cameraInstance.pinToObject.parent !== undefined && cameraInstance.pinToObject.parent.depth !== undefined) {
                            cameraInstance.depth = cameraInstance.pinToObject.parent.depth + cameraInstance.pinToObject.pinOffsetZ;
                        }
                    }
                    this.applyLayerZDepth(this.root);
                }
                applyLayerZDepth(parent) {
                    const cameraInstance = parent.___camera___instance;
                    const focalLength = 528.25;
                    const projectionCenter = {
                        'x': 0,
                        'y': 0
                    };
                    if (parent === this.root) {
                        const stageCenter = {
                            'x': this.library.properties.width / 2,
                            'y': this.library.properties.height / 2
                        };
                        projectionCenter.x = stageCenter.x;
                        projectionCenter.y = stageCenter.y;
                    }
                    for (const child in parent.children) {
                        const layerObj = parent.children[child];
                        if (layerObj === cameraInstance) {
                            continue;
                        }
                        this.applyLayerZDepth(layerObj, cameraInstance);
                        if (layerObj.layerDepth === undefined) {
                            continue;
                        }
                        if (layerObj.currentFrame !== layerObj.parent.currentFrame) {
                            layerObj.gotoAndPlay(layerObj.parent.currentFrame);
                        }
                        const matToApply = new createjs.Matrix2D();
                        let cameraMat = new createjs.Matrix2D();
                        let totalDepth = layerObj.layerDepth ? layerObj.layerDepth : 0;
                        let cameraDepth = 0;
                        if (cameraInstance && !layerObj.isAttachedToCamera) {
                            const mat = cameraInstance.getMatrix();
                            mat.tx -= projectionCenter.x;
                            mat.ty -= projectionCenter.y;
                            cameraMat = mat.invert();
                            cameraMat.prependTransform(projectionCenter.x, projectionCenter.y, 1, 1, 0, 0, 0, 0, 0);
                            cameraMat.appendTransform(-projectionCenter.x, -projectionCenter.y, 1, 1, 0, 0, 0, 0, 0);
                            if (cameraInstance.depth) {
                                cameraDepth = cameraInstance.depth;
                            }
                        }
                        if (layerObj.depth) {
                            totalDepth = layerObj.depth;
                        }
                        totalDepth -= cameraDepth;
                        if (totalDepth < -focalLength) {
                            matToApply.a = 0;
                            matToApply.d = 0;
                        } else {
                            if (layerObj.layerDepth) {
                                const sizeLockedMat = this.getProjectionMatrix(parent, layerObj.layerDepth);
                                if (sizeLockedMat) {
                                    sizeLockedMat.invert();
                                    matToApply.prependMatrix(sizeLockedMat);
                                }
                            }
                            matToApply.prependMatrix(cameraMat);
                            const projMat = this.getProjectionMatrix(parent, totalDepth);
                            if (projMat) {
                                matToApply.prependMatrix(projMat);
                            }
                        }
                        layerObj.transformMatrix = matToApply;
                    }
                }
                getProjectionMatrix(container, totalDepth) {
                    const focalLength = 528.25;
                    const projectionCenter = {
                        x: this.library.properties.width / 2,
                        y: this.library.properties.height / 2
                    };
                    const scale = (totalDepth + focalLength) / focalLength;
                    const scaleMat = new createjs.Matrix2D();
                    scaleMat.a = 1 / scale;
                    scaleMat.d = 1 / scale;
                    let projMat = new createjs.Matrix2D();
                    projMat.tx = -projectionCenter.x;
                    projMat.ty = -projectionCenter.y;
                    projMat = projMat.prependMatrix(scaleMat);
                    projMat.tx += projectionCenter.x;
                    projMat.ty += projectionCenter.y;
                    return projMat;
                }
                attachExportedComponents(library) {
                    this.exportedComponents = [];
                    library.addExportedComponent = component => {
                        this.exportedComponents.push(component);
                    };
                }
                getSuffixes() {
                    return [
                        '_text',
                        '_play',
                        '_anim'
                    ];
                }
                getNameSuffix(name) {
                    if (!name) {
                        return null;
                    }
                    let found = null;
                    this.getSuffixes().map(suffix => {
                        const index = name.lastIndexOf(suffix);
                        if (~index) {
                            found = suffix.substr(1);
                        }
                    });
                    return found;
                }
                getNameWithoutSuffix(name) {
                    if (!name) {
                        return null;
                    }
                    const index = name.lastIndexOf(this.getNameSuffix(name));
                    return name.substr(0, index - 1);
                }
                filterExportedComponents(exportedComponents) {
                    const components = {
                        'anim': {},
                        'text': {},
                        'play': []
                    };
                    exportedComponents.forEach(component => {
                        const suffix = this.getNameSuffix(component.name);
                        if (typeof components[suffix] !== 'undefined') {
                            if (suffix === 'play') {
                                components['play'].push(component);
                                return;
                            }
                            if (typeof components[suffix][component.name] !== 'undefined') {
                                for (let i = 1; i < 100000; i++) {
                                    const name = ''.concat(this.getNameWithoutSuffix(component.name), '_').concat(i, '_').concat(suffix);
                                    if (typeof components[suffix][name] === 'undefined') {
                                        component.name = name;
                                        components[suffix][name] = component;
                                        return;
                                    }
                                }
                            }
                            components[suffix][component.name] = component;
                        }
                    });
                    return components;
                }
                blink(widget) {
                    if (this.highlighted) {
                        const component = this.highlighted.component;
                        window.clearInterval(this.highlighted.blinker);
                        component.alpha = this.highlighted.alpha;
                        if (typeof component.gotoAndStop !== 'undefined') {
                            component.gotoAndStop(0);
                        }
                        this.highlighted = null;
                    }
                    if (!widget) {
                        return;
                    }
                    if (widget.parent === this.id) {
                        const category = this.components[this.getNameSuffix(widget.name)];
                        if (category && category[widget.name]) {
                            const component = category[widget.name];
                            const alpha = component.alpha;
                            const framecount = component.timeline ? component.timeline.duration - 1 : null;
                            let position = 0;
                            let direction = 1;
                            const blinker = window.setInterval(() => {
                                if (component.alpha > 1) {
                                    direction = 0;
                                } else if (component.alpha < 0.4) {
                                    direction = 1;
                                }
                                if (direction) {
                                    component.alpha = component.alpha + 0.08;
                                } else {
                                    component.alpha = component.alpha - 0.08;
                                }
                                if (framecount !== null) {
                                    position = (position + 1) % framecount;
                                    component.gotoAndStop(position);
                                }
                            }, 20);
                            this.highlighted = {
                                component,
                                blinker,
                                alpha
                            };
                        }
                    }
                }
                registerClickHandler(handler) {
                    this.deregisterClickHandlers();
                    const register = component => {
                        if (component.on == null) {
                            return;
                        }
                        this.onDblclickHandlers.push({
                            handler: component.on('dblclick', handler),
                            component
                        });
                    };
                    Object.values(this.components.anim).forEach(anim => register(anim));
                    Object.values(this.components.text).forEach(text => register(text));
                }
                deregisterClickHandlers() {
                    if (this.onDblclickHandlers == null) {
                        this.onDblclickHandlers = [];
                        return;
                    }
                    this.onDblclickHandlers.forEach(({
                                                         handler,
                                                         component
                                                     }) => {
                        component.off('dblclick', handler);
                    });
                    this.onDblclickHandlers = [];
                }
                resize() {
                    if (this.prevClientWidth === this.canvas.clientWidth && this.prevClientHeight === this.canvas.clientHeight) {
                        return;
                    }
                    this.prevClientWidth = this.canvas.clientWidth;
                    this.prevClientHeight = this.canvas.clientHeight;
                    const w = this.library.properties.width;
                    const h = this.library.properties.height;
                    const aspect = h / w;
                    const width = this.canvas.clientWidth;
                    const height = this.canvas.clientHeight;
                    const pRatio = window.devicePixelRatio || 1;
                    let xRatio = width / w;
                    let yRatio = height / h;
                    let sRatio = xRatio;
                    this.canvas.width = Math.floor(width * pRatio);
                    this.canvas.height = Math.floor(width * pRatio * aspect);
                    this.stage.scaleX = pRatio * sRatio;
                    this.stage.scaleY = pRatio * sRatio;
                    this.stage.tickOnUpdate = false;
                    this.stage.update();
                    this.stage.tickOnUpdate = true;
                }
                static getComponentNames(source, name) {
                    return new Promise((resolve, reject) => {
                        let runtime;
                        try {
                            runtime = new AnimateRuntime(name, source);
                        } catch (error) {
                            reject(error);
                            return;
                        }
                        const canvas = document.createElement('canvas');
                        runtime.init(canvas).then(() => {
                            const components = runtime.getComponents();
                            const out = {};
                            const categories = Object.keys(components);
                            categories.forEach(category => {
                                out[category] = [];
                                const nodes = Object.keys(components[category]);
                                nodes.forEach(node => {
                                    out[category].push(node);
                                });
                            });
                            runtime.destroy();
                            resolve(out);
                        });
                    });
                }
                static functionalizeSource(source) {
                    return Function('"use strict"; return('.concat(source, ')'))();
                }
            }
            function createAnimateRuntime(name, source, target, id) {
                return new Promise(resolve => {
                    const runtime = new AnimateRuntime(name, source, id);
                    runtime.init(target).then(() => {
                        resolve(runtime);
                    });
                });
            }

            ;

            config.widgets.animateAnims = {}
            class AnimateAnimContinuous extends Widget {
                constructor(configuration) {
                    super(configuration, 'AnimateAnimContinuous');
                    this.framecount = this.component.timeline.duration - 1;
                    this.position = 0;
                    this.tick = this.tick.bind(this);
                    createjs.Ticker.addEventListener('tick', this.tick);
                }
                getNextPosition(stopping = false) {
                    let value = this.value.value;
                    const min = this.min.value;
                    const max = this.max.value;
                    const maxspeed = this.maxspeed.value;
                    const minspeed = this.minspeed.value;
                    const reversed = this.reversed.value;
                    value = (value - min) / (max - min) * (maxspeed - minspeed) + minspeed;
                    if (reversed) {
                        value = -value;
                    }
                    if (stopping) {
                        const overflow = this.position + value > this.framecount;
                        const underflow = this.position + value < 0;
                        if (overflow || underflow) {
                            return 0;
                        }
                    }
                    let position = (this.position + value) % this.framecount;
                    if (position < 0) {
                        position = this.framecount + this.position;
                    }
                    return position;
                }
                tick() {
                    if (this.trigger.value) {
                        this.position = this.getNextPosition(false);
                        this.component.gotoAndStop(Math.floor(this.position));
                    } else {
                        if (this.position !== 0 && this.triggerFinish) {
                            this.position = this.getNextPosition(true);
                            this.component.gotoAndStop(Math.floor(this.position));
                        }
                    }
                }
                generateSetters() {
                    this.setters = {
                        value: () => {
                            if (this.value.function !== null) {
                                this.value.value = this.value.function(this.value.value);
                            }
                        },
                        trigger: () => {
                            if (this.trigger.function !== null) {
                                this.trigger.value = this.trigger.function(this.trigger.value);
                            }
                        },
                        min: () => {
                            if (this.min.function !== null) {
                                this.min.value = this.min.function(this.min.value);
                            }
                        },
                        max: () => {
                            if (this.max.function !== null) {
                                this.max.value = this.max.function(this.max.value);
                            }
                        },
                        minspeed: () => {
                            if (this.minspeed.function !== null) {
                                this.minspeed.value = this.minspeed.function(this.minspeed.value);
                            }
                        },
                        maxspeed: () => {
                            if (this.maxspeed.function !== null) {
                                this.maxspeed.value = this.maxspeed.function(this.maxspeed.value);
                            }
                        },
                        reversed: () => {
                            if (this.reversed.function !== null) {
                                this.reversed.value = this.reversed.function(this.reversed.value);
                            }
                        }
                    };
                }
                locateComponent() {
                    if (typeof this.animate === 'function') {
                        throw new ReferenceError('Widget ('.concat(this.name, ') of type (').concat(this.typeIdentifier, ') with ') + 'name="'.concat(this.name, '" could not be initialised because the relevant ') + 'Animate is not loaded.');
                    }
                    return this.animate.components.anim[this.name];
                }
            }
            class AnimateAnimControlled extends Widget {
                constructor(configuration) {
                    super(configuration, 'AnimateAnimControlled');
                    this.framecount = this.component.timeline.duration - 1;
                    this.originalAlpha = this.component.alpha;
                }
                generateSetters() {
                    this.setters = {
                        value: () => {
                            let value = this.value.value;
                            if (this.value.function !== null) {
                                value = this.value.function(this.value.value);
                            }
                            if (value < this.min.value) {
                                if (this.overflow.value === false) {
                                    value = this.min.value;
                                } else {
                                    this.setValue('min', value);
                                }
                            }
                            if (value > this.max.value) {
                                if (this.overflow.value === false) {
                                    value = this.max.value;
                                } else {
                                    this.setValue('max', value);
                                }
                            }
                            value = Math.floor((value - this.min.value) / (this.max.value - this.min.value) * this.framecount);
                            if (this.reversed.value === true) {
                                value = this.framecount - value;
                            }
                            this.component.gotoAndStop(value);
                        },
                        min: () => {
                            if (this.min.function !== null) {
                                this.min.value = this.min.function(this.min.value);
                            }
                        },
                        max: () => {
                            if (this.max.function !== null) {
                                this.max.value = this.max.function(this.max.value);
                            }
                        },
                        reversed: () => {
                            if (this.reversed.function !== null) {
                                this.reversed.value = this.reversed.function(this.reversed.value);
                            }
                        }
                    };
                }
                locateComponent() {
                    if (typeof this.animate === 'function') {
                        throw new ReferenceError('Widget ('.concat(this.name, ') of type (').concat(this.typeIdentifier, ') with ') + 'name="'.concat(this.name, '" could not be initialised because the relevant ') + 'Animate is not loaded.');
                    }
                    return this.animate.components.anim[this.name];
                }
                blink(up = 0.08, down = 0.08) {
                    const component = this.component;
                    if (component.alpha > 1) {
                        this.blinkDirection = 0;
                    } else if (component.alpha < 0.4) {
                        this.blinkDirection = 1;
                    }
                    if (this.blinkDirection) {
                        component.alpha = component.alpha + up;
                    } else {
                        component.alpha = component.alpha - down;
                    }
                }
                stopBlinking() {
                    this.component.alpha = this.originalAlpha;
                    this.blinkDirection = 0;
                }
            }
            function initAnimateAnimsContinuous() {
                return new Promise(resolve => {
                    const animatelist = config.widgets.animateAnims;
                    Object.entries(animatelist).forEach(([animateId, widgetlist]) => {
                        const animate = animates[animateId];
                        Object.entries(widgetlist.continuous).forEach(([id, configuration]) => {
                            configuration.animate = animate;
                            try {
                                const widget = new AnimateAnimContinuous(configuration);
                                if (widget.constructed === true) {
                                    widgets[configuration.id] = widget;
                                }
                            } catch (e) {
                                if (e instanceof ReferenceError) {
                                    console.warn(e.message);
                                } else {
                                    console.warn(e);
                                }
                            }
                        });
                    });
                    resolve();
                });
            }
            function initAnimateAnimsControlled() {
                return new Promise(resolve => {
                    const animatelist = config.widgets.animateAnims;
                    Object.entries(animatelist).forEach(([animateId, widgetlist]) => {
                        const animate = animates[animateId];
                        Object.entries(widgetlist.controlled).forEach(([id, configuration]) => {
                            configuration.animate = animate;
                            try {
                                const widget = new AnimateAnimControlled(configuration);
                                if (widget.constructed === true) {
                                    widgets[configuration.id] = widget;
                                }
                            } catch (e) {
                                if (e instanceof ReferenceError) {
                                    console.warn(e.message);
                                } else {
                                    console.warn(e);
                                }
                            }
                        });
                    });
                    resolve();
                });
            }

            ;
            function initAnimatePlays() {
                return new Promise(resolve => {
                    Object.entries(animates).forEach(([id, animate]) => {
                        if (animate.components === undefined) {
                            return;
                        }
                        animate.components.play.forEach(play => {
                            play.onTick = evtObj => {
                                play._tick(evtObj);
                            };
                            createjs.Ticker.addEventListener('tick', play.onTick);
                        });
                    });
                });
            }
            ;

            config.widgets.animateTexts = {}
            class AnimateText extends Widget {
                constructor(configuration) {
                    super(configuration, 'AnimateText');
                }
                generateSetters() {
                    this.setters = {
                        value: () => {
                            if (this.value.function === null) {
                                this.component.text = this.value.value;
                            } else {
                                this.component.text = this.value.function(this.value.value);
                            }
                        },
                        visible: () => {
                            if (this.visible.function === null) {
                                this.component.visible = this.visible.value;
                            } else {
                                this.component.visible = this.visible.function(this.visible.value);
                            }
                        }
                    };
                }
                locateComponent() {
                    if (typeof this.animate === 'function') {
                        throw new ReferenceError('Widget ('.concat(this.name, ') of type (').concat(this.typeIdentifier, ') with ') + 'name="'.concat(this.name, '" could not be initialised because the relevant ') + 'Animate is not loaded.');
                    }
                    return this.animate.components.text[this.name];
                }
            }
            function initAnimateTexts() {
                return new Promise(resolve => {
                    const animatelist = config.widgets.animateTexts;
                    Object.entries(animatelist).forEach(([animateName, widgetlist]) => {
                        const animate = animates[animateName];
                        Object.entries(widgetlist).forEach(([name, configuration]) => {
                            configuration.animate = animate;
                            try {
                                widgets[configuration.id] = new AnimateText(configuration);
                            } catch (e) {
                                if (e instanceof ReferenceError) {
                                    console.warn(e.message);
                                } else {
                                    throw e;
                                }
                            }
                        });
                    });
                    resolve();
                });
            }

            ;

            config.widgets.buttons = {
                'values-reset-btn-id':{
                    'name': 'Reset with new values',
                    'mode': 'click',
                    'target': {
                        'value': null,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null,
                        'typeof': false
                    },
                    'events': [
                        'click',
                        'press',
                        'release'
                    ],
                    'actions': {
                        '4e10d64d-9e2c-4c26-9cc7-27fdcb26d288': {
                            'id': '4e10d64d-9e2c-4c26-9cc7-27fdcb26d288',
                            'event': 'click',
                            'function': function() {
                                config.actions['new-values-reset-action-id'].bind(this)(getModelByID('model-id'));
                            }
                        }
                    },
                    'attributes': [
                        'label',
                        'enabled',
                        'visible',
                        'onClick',
                        'onPress',
                        'onRelease'
                    ],
                    'label': {
                        'typeof': 'string',
                        'value': 'Reset with new values',
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'enabled': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'visible': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onClick': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onPress': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onRelease': {
                        'typeof': 'number',
                        'value': 0,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'id': 'values-reset-btn-id'
                },
                'play-btn-id': {
                    'name': 'Spustiť',
                    'mode': 'click',
                    'target': {
                        'value': null,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null,
                        'typeof': false
                    },
                    'events': [
                        'click',
                        'press',
                        'release'
                    ],
                    'actions': {
                        '4e10d64d-9e2c-4c26-9cc7-27fdcb26d288': {
                            'id': '4e10d64d-9e2c-4c26-9cc7-27fdcb26d288',
                            'event': 'click',
                            'function': function() {
                                config.actions['play-action-id'].bind(this)(getModelByID('model-id'));
                            }
                        }
                    },
                    'attributes': [
                        'label',
                        'enabled',
                        'visible',
                        'onClick',
                        'onPress',
                        'onRelease'
                    ],
                    'label': {
                        'typeof': 'string',
                        'value': 'Start',
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'enabled': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'visible': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onClick': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onPress': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onRelease': {
                        'typeof': 'number',
                        'value': 0,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'id': 'play-btn-id'
                },
                'pause-btn-id': {
                    'name': 'Stop',
                    'mode': 'click',
                    'target': {
                        'value': null,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null,
                        'typeof': false
                    },
                    'events': [
                        'click',
                        'press',
                        'release'
                    ],
                    'actions': {
                        'b19f8271-636e-4084-8d5d-6cbe115b77f4': {
                            'id': 'b19f8271-636e-4084-8d5d-6cbe115b77f4',
                            'event': 'click',
                            'function': function() {
                                config.actions['pause-action-id'].bind(this)(getModelByID('model-id'));
                            }
                        }
                    },
                    'attributes': [
                        'label',
                        'enabled',
                        'visible',
                        'onClick',
                        'onPress',
                        'onRelease'
                    ],
                    'label': {
                        'typeof': 'string',
                        'value': 'Stop',
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'enabled': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'visible': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onClick': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onPress': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onRelease': {
                        'typeof': 'number',
                        'value': 0,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'id': 'pause-btn-id'
                },
                'clear-btn-id': {
                    'name': 'Reset',
                    'mode': 'click',
                    'target': {
                        'value': null,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null,
                        'typeof': false
                    },
                    'events': [
                        'click',
                        'press',
                        'release'
                    ],
                    'actions': {
                        'c27b7919-95e0-408c-aba6-988e0bf12621': {
                            'id': 'c27b7919-95e0-408c-aba6-988e0bf12621',
                            'event': 'click',
                            'function': function() {
                                config.actions['graf-clear-action-id'].bind(this)(getChartByID('chart-id'));
                            }
                        },
                        '4e544e0c-b71a-4e7d-8745-11c18715c111': {
                            'id': '4e544e0c-b71a-4e7d-8745-11c18715c111',
                            'event': 'click',
                            'function': function() {
                                config.actions['initial-reset-action-id'].bind(this)(getModelByID('model-id'));
                            }
                        }
                    },
                    'attributes': [
                        'label',
                        'enabled',
                        'visible',
                        'onClick',
                        'onPress',
                        'onRelease'
                    ],
                    'label': {
                        'typeof': 'string',
                        'value': 'Reset',
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'enabled': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'visible': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onClick': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onPress': {
                        'typeof': 'number',
                        'value': 1,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'onRelease': {
                        'typeof': 'number',
                        'value': 0,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'id': 'clear-btn-id'
                }
            }

            class Button extends Widget {
                constructor(configuration) {
                    super(configuration, 'button');
                    this.handleOnClick = this.handleOnClick.bind(this);
                    this.handleOnPress = this.handleOnPress.bind(this);
                    this.handleOnRelease = this.handleOnRelease.bind(this);
                    if (this.mode === 'press') {
                        this.component.addEventListener('mousedown', this.handleOnPress);
                        this.component.addEventListener('keydown', this.handleOnPress);
                        this.component.addEventListener('mouseup', this.handleOnRelease);
                        this.component.addEventListener('keyup', this.handleOnRelease);
                    } else if (this.mode === 'click') {
                        this.component.addEventListener('click', this.handleOnClick);
                    }
                    this.triggerPress = this.triggerPress.bind(this);
                    this.triggerRelease = this.triggerRelease.bind(this);
                    this.triggerClick = this.triggerClick.bind(this);
                    this.component.addEventListener('mousedown', this.triggerPress);
                    this.component.addEventListener('keydown', this.triggerPress);
                    this.component.addEventListener('mouseup', this.triggerRelease);
                    this.component.addEventListener('keyup', this.triggerRelease);
                    this.component.addEventListener('click', this.triggerClick);
                    this.addValueProvider('target', this.target.provider);
                }
                triggerPress() {
                    this.dispatchEvent(new Event('press'));
                }
                triggerRelease() {
                    this.dispatchEvent(new Event('release'));
                }
                triggerClick() {
                    this.dispatchEvent(new Event('click'));
                }
                handleOnClick() {
                    if (this.target.provider !== null) {
                        this.target.provider.setValue(this.target.reference, this.onClick.value);
                    }
                }
                handleOnPress() {
                    if (this.target.provider !== null) {
                        this.target.provider.setValue(this.target.reference, this.onPress.value);
                    }
                }
                handleOnRelease() {
                    if (this.target.provider !== null) {
                        this.target.provider.setValue(this.target.reference, this.onRelease.value);
                    }
                }
                generateSetters() {
                    this.setters = {
                        enabled: () => {
                            if (this.enabled.function === null) {
                                this.component.disabled = !this.enabled.value;
                                return;
                            }
                            this.component.disabled = !this.enabled.function(this.enabled.value);
                        },
                        visible: () => {
                            if (this.visible.function === null) {
                                this.component.hidden = !this.visible.value;
                                return;
                            }
                            this.component.hidden = !this.visible.function(this.visible.value);
                        },
                        label: () => {
                            if (this.label.function === null) {
                                this.component.innerHTML = this.label.value;
                                return;
                            }
                            this.component.innerHTML = this.label.function(this.label.value);
                        },
                        onClick: () => {
                            if (this.onClick.function !== null) {
                                this.onClick.value = this.onClick.function(this.onClick.value);
                            }
                        },
                        onPress: () => {
                            if (this.onPress.function !== null) {
                                this.onPress.value = this.onPress.function(this.onPress.value);
                            }
                        },
                        onRelease: () => {
                            if (this.onRelease.function !== null) {
                                this.onRelease.value = this.onRelease.function(this.onRelease.value);
                            }
                        }
                    };
                }
                setValueProvider(attribute, id, target) {
                    if (attribute === 'target') {
                        this.target.reference = target.registerValueSetter(this, id, attribute);
                        this.target.provider = target;
                        return;
                    }
                    super.setValueProvider(attribute, id, target);
                }
            }
            function initButtons() {
                return new Promise(resolve => {
                    const buttons = config.widgets.buttons;
                    Object.entries(buttons).forEach(([name, configuration]) => {
                        let button;
                        try {
                            button = new Button(configuration);
                        } catch (e) {
                            if (e instanceof ReferenceError) {
                                console.warn(e.message);
                                return;
                            } else {
                                throw e;
                            }
                        }
                        widgets[button.id] = button;
                    });
                    resolve();
                });
            }

            ;

            config.widgets.charts = {
                'chart-id': {
                    'name': 'model-chart',
                    'library': 'plotly',
                    'xaxes': {
                        'xaxis': {
                            'visible': true,
                            'color': '#444',
                            'title': '',
                            'type': 'linear',
                            'autorange': true,
                            'rangemode': 'normal',
                            'range': null,
                            'fixedrange': false,
                            'tickmode': 'auto',
                            'nticks': 0,
                            'tickvals': null,
                            'ticktext': null,
                            'ticks': null,
                            'mirror': null,
                            'ticklen': 5,
                            'tickwidth': 1,
                            'tickcolor': '#444',
                            'tickfont': null,
                            'tickangle': 'auto',
                            'tickprefix': '',
                            'showtickprefix': 'all',
                            'ticksuffix': '',
                            'showticksuffix': 'all',
                            'showexponent': 'all',
                            'exponentformat': 'e',
                            'separatethousands': false,
                            'showticklabels': true,
                            'automargin': true,
                            'showspikes': false,
                            'spikethickness': 3,
                            'spikedash': 'dash',
                            'spikemode': 'toaxis',
                            'spikesnap': 'data',
                            'showline': false,
                            'linecolor': '#444',
                            'linewidth': 1,
                            'showgrid': true,
                            'gridcolor': '#eee',
                            'gridwidth': 1,
                            'zeroline': true,
                            'zerolinecolor': '#444',
                            'zerolinewidth': 1,
                            'side': 'left',
                            'rangeslider': null,
                            'rangeselector': null
                        }
                    },
                    'yaxes': {
                        'yaxis': {
                            'visible': true,
                            'color': '#444',
                            'title': '',
                            'type': 'linear',
                            'autorange': true,
                            'rangemode': 'normal',
                            'range': null,
                            'fixedrange': false,
                            'tickmode': 'auto',
                            'nticks': 0,
                            'tickvals': null,
                            'ticktext': null,
                            'ticks': null,
                            'mirror': null,
                            'ticklen': 5,
                            'tickwidth': 1,
                            'tickcolor': '#444',
                            'tickfont': null,
                            'tickangle': 'auto',
                            'tickprefix': '',
                            'showtickprefix': 'all',
                            'ticksuffix': '',
                            'showticksuffix': 'all',
                            'showexponent': 'all',
                            'exponentformat': 'e',
                            'separatethousands': false,
                            'showticklabels': true,
                            'automargin': true,
                            'showspikes': false,
                            'spikethickness': 3,
                            'spikedash': 'dash',
                            'spikemode': 'toaxis',
                            'spikesnap': 'data',
                            'showline': false,
                            'linecolor': '#444',
                            'linewidth': 1,
                            'showgrid': true,
                            'gridcolor': '#eee',
                            'gridwidth': 1,
                            'zeroline': true,
                            'zerolinecolor': '#444',
                            'zerolinewidth': 1,
                            'side': 'left',
                            'rangeslider': null,
                            'rangeselector': null
                        }
                    },
                    'margin': {
                        'l': 50,
                        'r': 20,
                        'b': 20,
                        't': 20,
                        'pad': 4
                    },
                    'legend': {
                        'bgcolor': '',
                        'bordercolor': '#444',
                        'borderwidth': 0,
                        'font': {
                            'family': '',
                            'size': '',
                            'color': ''
                        },
                        'orientation': 'v',
                        'traceorder': 'normal',
                        'tracegroupgap': 10,
                        'x': 1.02,
                        'y': 1,
                        'xanchor': 'left',
                        'yanchor': 'auto'
                    },
                    'fps': 20,
                    'plot_bgcolor': '#FFFFFF00',
                    'paper_bgcolor': '#FFFFFF00',
                    'events': ['change'],
                    'actions': {},
                    'annotations': {},
                    'datasets': datasets,
                    'shapes': {},
                    'images': {},
                    'enabled': {
                        'typeof': 'boolean',
                        'value': true,
                        'complex': false,
                        'provider': null,
                        'array': false,
                        'indexes': null,
                        'function': null
                    },
                    'id': 'chart-id'
                }
            }
            class PlotlyBase extends Widget {
                constructor(configuration, typeIdentifier) {
                    super(configuration, typeIdentifier);
                    this.initAdditionals('shapes', this.shapes, this.shapeIndexes);
                    this.initAdditionals('annotations', this.annotations, this.annotationIndexes);
                    this.initAdditionals('images', this.images, this.imageIndexes);
                }
                updateComponent() {
                    super.updateComponent();
                    this.initPlotly();
                }
                parseAttribute(attribute) {
                    if (attribute.startsWith('{')) {
                        return JSON.parse(attribute);
                    }
                    return null;
                }
                initAdditionals(identifier, config, indexes) {
                    if (config == null) {
                        return;
                    }
                    if (Object.values(config) === 0) {
                        return;
                    }
                    const complex = [];
                    Object.entries(config).forEach(([id, item]) => {
                        Object.entries(item).forEach(([name, value]) => {
                            if (typeof value['complex'] !== 'undefined' && value['complex']) {
                                complex.push({
                                    id,
                                    name
                                });
                            }
                        });
                    });
                    complex.forEach(({
                                         id,
                                         name
                                     }) => {
                        this.addValueProvider(JSON.stringify({
                            identifier,
                            id,
                            name,
                            setter: ''.concat(identifier, '-').concat(name)
                        }), config[id][name].provider);
                        this.setters[''.concat(identifier, '-').concat(name)] = item => {
                            if (item == null) {
                                return;
                            }
                            const attr = config[item][name];
                            if (attr.function !== null) {
                                attr.value = attr.function(attr.value);
                            }
                            const itemIdentifier = ''.concat(identifier, '[').concat(indexes[item], '].').concat(name);
                            Plotly.relayout(this.plotly, {
                                [itemIdentifier]: attr.value
                            });
                        };
                    });
                }
                initShapes() {
                    this.shapes = this.shapes || {};
                    this.shapeIndexes = [];
                    const shapes = [];
                    let index = 1;
                    Object.entries(this.shapes).forEach(([id, shape]) => {
                        this.shapeIndexes[id] = index;
                        shapes[index] = {
                            type: shape.type,
                            name: shape.name,
                            xref: shape.xref,
                            yref: shape.yref,
                            layer: shape.layer,
                            x0: shape.x0.value,
                            x1: shape.x1.value,
                            y0: shape.y0.value,
                            y1: shape.y1.value,
                            visible: shape.visible.value,
                            opacity: shape.opacity.value,
                            fillcolor: shape.fillcolor.value,
                            line: {
                                color: shape.color.value,
                                width: shape.width.value,
                                dash: shape.dash.value
                            }
                        };
                        index++;
                    });
                    return shapes;
                }
                initAnnotations() {
                    this.annotations = this.annotations || {};
                    this.annotationIndexes = [];
                    const annotations = [];
                    let index = 1;
                    Object.entries(this.annotations).forEach(([id, annotation]) => {
                        this.annotationIndexes[id] = index;
                        annotations[index] = {
                            xref: annotation.xref,
                            yref: annotation.yref,
                            x: annotation.x.value,
                            y: annotation.y.value,
                            visible: annotation.visible.value,
                            opacity: annotation.opacity.value,
                            font: {
                                family: annotation.family.value,
                                size: annotation.size.value,
                                color: annotation.color.value
                            },
                            bgcolor: annotation.bgcolor.value,
                            bordercolor: annotation.bordercolor.value,
                            width: annotation.width.value,
                            height: annotation.height.value,
                            text: annotation.text.value,
                            showarrow: false
                        };
                        index++;
                    });
                    return annotations;
                }
                initImages() {
                    this.images = this.images || {};
                    this.imageIndexes = [];
                    const images = [];
                    let index = 1;
                    Object.entries(this.images).forEach(([id, image]) => {
                        this.imageIndexes[id] = index;
                        images[index] = {
                            name: image.name,
                            source: image.source,
                            xref: image.xref,
                            yref: image.yref,
                            sizing: image.sizing,
                            x: image.x.value,
                            y: image.y.value,
                            sizex: image.sizex.value,
                            sizey: image.sizey.value,
                            visible: image.visible.value,
                            opacity: image.opacity.value,
                            layer: image.layer,
                            xanchor: image.xanchor,
                            yanchor: image.yanchor
                        };
                        index++;
                    });
                    return images;
                }
            }
            class PlotlyChart extends PlotlyBase {
                constructor(configuration) {
                    super(configuration, 'chart');
                    Object.entries(this.datasets).forEach(([id, dataset]) => {
                        this.addValueProvider(JSON.stringify({
                            dataset: id,
                            axis: 'x'
                        }), dataset.x.provider);
                        this.addValueProvider(JSON.stringify({
                            dataset: id,
                            axis: 'y'
                        }), dataset.y.provider);
                        this.addValueProvider(JSON.stringify({
                            dataset: id,
                            type: 'maxSamples'
                        }), dataset.maxSamples.provider);
                    });
                    this.oneshotBufferUpdateTraces = this.oneshotBufferUpdateTraces.bind(this);
                    this.tick = this.tick.bind(this);
                    window.setInterval(this.tick, 1000 / this.fps);
                }
                setValueProvider(attribute, id, target) {
                    const attr = this.parseAttribute(attribute);
                    const dataset = this.datasets[attr.dataset];
                    if (attr && attr.dataset) {
                        const axis = dataset[attr.axis];
                        if (axis.array) {
                            target.registerArrayListener(this, axis.indexes, attribute, true);
                        } else {
                            target.registerValueListener(this, id, attribute, true);
                        }
                        axis.actualProvider = target;
                        axis.attribute = attribute;
                    } else {
                        target.registerValueListener(this, id, attribute);
                        target.registerInitialValueListener(this, id, attribute);
                        if (attr.dataset) {
                            dataset[attr.type].actualProvider = target;
                            dataset[attr.type].attribute = attribute;
                        }
                    }
                }
                generateSetters() {
                    this.setters = {
                        enabled: () => {
                            if (this.enabled.function !== null) {
                                this.enabled.value = this.enabled.function(this.enabled.value);
                            }
                        },
                        maxSamples: dataset => {
                            if (dataset) {
                                if (this.datasets[dataset].maxSamples.function !== null) {
                                    this.datasets[dataset].maxSamples.value = this.datasets[dataset].maxSamples.function(this.datasets[dataset].maxSamples.value);
                                }
                            }
                        },
                        offset: dataset => {
                            if (dataset) {
                                if (this.datasets[dataset].offset.function !== null) {
                                    this.datasets[dataset].offset.value = this.datasets[dataset].offset.function(this.datasets[dataset].offset.value);
                                }
                            }
                        }
                    };
                }
                initTraces() {
                    this.indexes = {};
                    const data = [];
                    let index = 0;
                    const getAxisId = name => {
                        return ''.concat(name.substring(0, 1)).concat(name.replace(/\D/g, ''));
                    };
                    Object.entries(this.datasets).forEach(([id, dataset]) => {
                        this.indexes[id] = index;
                        this.buffer[id] = {
                            current: null,
                            x: null,
                            y: null
                        };
                        this.oneshotBuffer[id] = {
                            x: null,
                            y: null
                        };
                        data[index] = {
                            x: [],
                            xaxis: getAxisId(dataset.xaxis),
                            y: [],
                            yaxis: getAxisId(dataset.yaxis),
                            type: 'scatter',
                            mode: dataset.mode,
                            name: dataset.name,
                            line: dataset.line,
                            fill: dataset.fill,
                            showlegend: dataset.showlegend
                        };
                        Object.entries(dataset.other).forEach(([key, value]) => {
                            data[index][key] = value;
                        });
                        index++;
                    });
                    return data;
                }
                initPlotly() {
                    if (this.plotly === null || this.plotly === undefined) {
                        const d3 = Plotly.d3;
                        var div = d3.select(this.component).style({
                            width: '100%',
                            height: '100%'
                        });
                        this.plotly = div.node();
                        this.buffer = {};
                        this.oneshotBuffer = {};
                        this.oneshotBufferUpdateTracesTimeout = false;
                        const data = this.initTraces();
                        const shapes = this.initShapes();
                        const annotations = this.initAnnotations();
                        const images = this.initImages();
                        let layout = {
                            margin: this.margin,
                            legend: this.legend,
                            plot_bgcolor: this.plot_bgcolor,
                            paper_bgcolor: this.paper_bgcolor,
                            shapes,
                            annotations,
                            images
                        };
                        layout = Object.assign(layout, this.xaxes);
                        layout = Object.assign(layout, this.yaxes);
                        const config = {
                            'displayModeBar': false,
                            'responsive': true
                        };
                        Plotly.newPlot(this.plotly, data, layout, config);
                        Plotly.Plots.resize(this.plotly);
                        this.initialized = true;
                    }
                }
                updateTrace(id, axis, values, time) {
                    if (this.enabled.value === false) {
                        return;
                    }
                    const altAxis = axis === 'x' ? 'y' : 'x';
                    if (this.datasets[id][altAxis].time) {
                        const update = {
                            y: time,
                            x: time
                        };
                        update[axis] = values;
                        this.oneshotBuffer[id] = update;
                    } else {
                        this.oneshotBuffer[id][axis] = values;
                    }
                    if (this.oneshotBufferUpdateTracesTimeout === false) {
                        this.oneshotBufferUpdateTracesTimeout = window.setTimeout(this.oneshotBufferUpdateTraces, 50);
                    }
                }
                oneshotBufferUpdateTraces() {
                    const buf = this.oneshotBuffer;
                    const tracesToUpdate = [];
                    const update = {
                        x: [],
                        y: []
                    };
                    Object.entries(buf).forEach(([id, data]) => {
                        const index = this.indexes[id];
                        if (data.x === null || data.y === null) {
                            return;
                        }
                        const maxSamples = Number(this.datasets[id].maxSamples.value);
                        const length = data.x.length;
                        if (maxSamples > 0 && length > maxSamples) {
                            data.x = data.x.slice(length - maxSamples);
                            data.y = data.y.slice(length - maxSamples);
                        }
                        tracesToUpdate.push(index);
                        update.x.push(data.x);
                        update.y.push(data.y);
                        this.oneshotBuffer[id].x = null;
                        this.oneshotBuffer[id].y = null;
                    });
                    Plotly.restyle(this.plotly, update, tracesToUpdate);
                    this.oneshotBufferUpdateTracesTimeout = false;
                    this.dispatchEvent(new Event('change'));
                }
                offsetArrayTime(attribute, array, time) {
                    const attr = JSON.parse(attribute);
                    if (Array.isArray(time) === false) {
                        time = [...Array(array.length).keys()];
                    }
                    const dataset = this.datasets[attr.dataset];
                    if (dataset.offset != null && dataset.offset.value > 0) {
                        time = time.map(val => parseFloat(val) + parseFloat(dataset.offset.value));
                    }
                    return time;
                }
                setArray(attribute, array, time) {
                    time = this.offsetArrayTime(attribute, array, time);
                    this.setValue(attribute, array, time);
                }
                setArrays(attribute, arrays, times) {
                    const array = arrays[arrays.length - 1];
                    const time = this.offsetArrayTime(attribute, array, times[times.length - 1]);
                    this.setValues(attribute, array, time);
                }
                getIdFromIndex(index) {
                    let id = null;
                    Object.entries(this.indexes).forEach(entry => {
                        if (entry[1] === index) {
                            id = entry[0];
                        }
                    });
                    return id;
                }
                clearBuffer(id) {
                    this.buffer[id] = {
                        current: null,
                        x: null,
                        y: null
                    };
                    this.oneshotBuffer[id] = {
                        x: null,
                        y: null
                    };
                }
                clearTrace(index) {
                    const id = this.getIdFromIndex(index);
                    if (id != null) {
                        this.clearBuffer(id);
                    }
                    Plotly.restyle(this.plotly, {
                        x: [
                            []
                        ],
                        y: [
                            []
                        ]
                    }, [index]);
                }
                clear() {
                    this.plotly = null;
                    this.initPlotly();
                }
                enableTrace(id) {
                    const dataset = this.datasets[id];
                    const enableListener = attr => {
                        if (attr.actualProvider != null && attr.actualProvider['enableListener'] != null) {
                            const provider = attr.actualProvider;
                            provider.enableListener(this, attr.attribute);
                        }
                    };
                    enableListener(dataset.x);
                    enableListener(dataset.y);
                    enableListener(dataset.maxSamples);
                }
                disableTrace(id) {
                    const dataset = this.datasets[id];
                    const disableListener = attr => {
                        if (attr.actualProvider != null && attr.actualProvider['disableListener'] != null) {
                            const provider = attr.actualProvider;
                            provider.disableListener(this, attr.attribute);
                        }
                    };
                    disableListener(dataset.x);
                    disableListener(dataset.y);
                    disableListener(dataset.maxSamples);
                }
                setTraceVisibility(index, visible) {
                    Plotly.restyle(this.plotly, {
                        visible
                    }, [index]);
                }
                enableTraceUpdates(index) {
                    const id = this.getIdFromIndex(index);
                    this.enableTrace(id);
                }
                disableTraceUpdates(index) {
                    const id = this.getIdFromIndex(index);
                    this.disableTrace(id);
                }
                deleteTrace(index) {
                    const id = this.getIdFromIndex(index);
                    if (id) {
                        this.indexes[id] = null;
                        this.disableTrace(id);
                    }
                    Plotly.deleteTraces(this.plotly, index);
                }
                deleteLastTrace() {
                    if (this.plotly.data.length > 0) {
                        this.deleteTrace(this.plotly.data.length - 1);
                    }
                }
                duplicateTrace(index, randomizeColor = true) {
                    if (this.plotly.data[index] == null) {
                        console.warn('Can not duplicate trace, no such index '.concat(index));
                        return;
                    }
                    const trace = this.plotly.data[index];
                    const duplicated = Object.assign({}, trace);
                    if (randomizeColor) {
                        duplicated.line = Object.assign({}, trace.line);
                        duplicated.line.color = null;
                    }
                    Plotly.addTraces(this.plotly, [duplicated]);
                }
                getTraces() {
                    return this.plotly.data;
                }
                setValues(attribute, values, time) {
                    const lastValue = values[values.length - 1];
                    if (attribute.startsWith('{')) {
                        const attr = JSON.parse(attribute);
                        if (attr.axis) {
                            if (this.datasets[attr.dataset][attr.axis].function !== null) {
                                let transformed = [];
                                values.forEach(value => {
                                    transformed.push(this.datasets[attr.dataset][attr.axis].function(value));
                                });
                                this.updateTrace(attr.dataset, attr.axis, transformed, time);
                            } else {
                                this.updateTrace(attr.dataset, attr.axis, values, time);
                            }
                            return;
                        }
                        if (attr.dataset) {
                            this.datasets[attr.dataset][attr.type].value = lastValue;
                            this.setters[attr.type](attr.dataset);
                            return;
                        }
                        if (attr.identifier) {
                            this[attr.identifier][attr.id][attr.name].value = lastValue;
                            this.setters[attr.setter](attr.id);
                        }
                    }
                }
                setValue(attribute, value, time) {
                    if (attribute.startsWith('{')) {
                        const attr = JSON.parse(attribute);
                        if (attr.axis) {
                            if (this.datasets[attr.dataset][attr.axis].function !== null) {
                                value = this.datasets[attr.dataset][attr.axis].function(value);
                            }
                            if (this.enabled.value) {
                                if (!Array.isArray(value)) {
                                    this.appendToDataset(attr.dataset, attr.axis, value, time);
                                } else {
                                    this.replaceDataset(attr.dataset, attr.axis, value, time);
                                }
                            }
                            return;
                        }
                        if (attr.dataset) {
                            this.datasets[attr.dataset][attr.type].value = value;
                            this.setters[attr.type](attr.dataset);
                            return;
                        }
                        if (attr.identifier) {
                            this[attr.identifier][attr.id][attr.name].value = value;
                            this.setters[attr.setter](attr.id);
                            return;
                        }
                    }
                    super.setValue(attribute, value, time);
                }
                appendToDataset(id, axis, value, time) {
                    const altAxis = axis === 'x' ? 'y' : 'x';
                    const buffer = this.buffer;
                    if (this.datasets[id][altAxis].time) {
                        buffer[id][axis] ? buffer[id][axis].push(value) : buffer[id][axis] = [value];
                        buffer[id][altAxis] ? buffer[id][altAxis].push(time) : buffer[id][altAxis] = [time];
                    } else {
                        if (buffer[id].current == null) {
                            buffer[id].current = {
                                x: null,
                                y: null
                            };
                        }
                        const current = buffer[id].current;
                        if (current[axis] != null) {
                            console.warn('Discarding '.concat(axis, ' data point id:').concat(id, ' value:').concat(value, ' for time:').concat(time, '. ') + 'This usually happens when one of the axes of the XY chart is updating faster ' + 'than the other one. Chart is now probably in an inconsistent state.`');
                        }
                        current[axis] = value;
                        if (current[axis] != null && current[altAxis] != null) {
                            if (buffer[id][axis] == null) {
                                buffer[id][axis] = [];
                            }
                            if (buffer[id][altAxis] == null) {
                                buffer[id][altAxis] = [];
                            }
                            buffer[id][axis].push(current[axis]);
                            buffer[id][altAxis].push(current[altAxis]);
                            buffer[id].current = null;
                        }
                    }
                }
                replaceDataset(id, axis, values, time) {
                    const altAxis = axis === 'x' ? 'y' : 'x';
                    const buffer = this.buffer;
                    if (this.datasets[id][altAxis].time) {
                        buffer[id][axis] = this.truncateDatapoints(id, values);
                        buffer[id][altAxis] = this.truncateDatapoints(id, time);
                        buffer[id].replace = true;
                    } else {
                        if (buffer[id].current == null) {
                            buffer[id].current = {
                                x: null,
                                y: null
                            };
                        }
                        const current = buffer[id].current;
                        if (current[axis] != null) {
                            console.warn('Discarding '.concat(axis, ' data points id:').concat(id, ' values:').concat(values, ' for time:').concat(time, '. ') + 'This usually happens when one of the axes of the XY chart is updating faster ' + 'than the other one. Chart is now probably in an inconsistent state.`');
                        }
                        current[axis] = values;
                        if (current[axis] != null && current[altAxis] != null) {
                            if (buffer[id][axis] == null) {
                                buffer[id][axis] = [];
                            }
                            if (buffer[id][altAxis] == null) {
                                buffer[id][altAxis] = [];
                            }
                            buffer[id][axis] = current[axis];
                            buffer[id][altAxis] = current[altAxis];
                            buffer[id].current = null;
                            buffer[id].replace = true;
                        }
                    }
                }
                truncateDatapoints(id, values) {
                    const maxSamples = Number(this.datasets[id].maxSamples.value);
                    const length = values.length;
                    if (maxSamples > 0 && length > maxSamples) {
                        return values.slice(length - maxSamples);
                    }
                    return values;
                }
                tick() {
                    this.flushBuffer();
                }
                flushBuffer() {
                    const extend = {
                        x: [],
                        y: []
                    };
                    const replace = {
                        x: [],
                        y: []
                    };
                    const indexes = {
                        extend: [],
                        replace: []
                    };
                    const maxSamples = {
                        x: [],
                        y: []
                    };
                    Object.entries(this.buffer).forEach(([id, buffer]) => {
                        if (buffer.x && buffer.y) {
                            if (buffer.replace) {
                                replace.x.push(buffer.x);
                                replace.y.push(buffer.y);
                                indexes.replace.push(this.indexes[id]);
                            } else {
                                extend.x.push(buffer.x);
                                extend.y.push(buffer.y);
                                indexes.extend.push(this.indexes[id]);
                                maxSamples.x.push(this.datasets[id].maxSamples.value);
                                maxSamples.y.push(this.datasets[id].maxSamples.value);
                            }
                            this.buffer[id] = {
                                current: null,
                                x: null,
                                y: null
                            };
                        }
                    });
                    if (indexes.extend.length > 0) {
                        Plotly.extendTraces(this.plotly, {
                            x: extend.x,
                            y: extend.y
                        }, indexes.extend, maxSamples);
                    }
                    if (indexes.replace.length > 0) {
                        Plotly.restyle(this.plotly, replace, indexes.replace);
                    }
                }
            }
            class Gamblegram extends PlotlyBase {
                constructor(configuration) {
                    super(configuration, 'chart');
                }
                sort(obj) {
                    return Object.values(obj).sort((a, b) => a.position - b.position);
                }
                setValueProvider(attribute, id, target) {
                    const attr = this.parseAttribute(attribute);
                    if (attr == null) {
                        return super.setValueProvider(attribute, id, target);
                    }
                    target.registerValueListener(this, id, attribute);
                }
                generateSetters() {
                    this.setters = {
                        enabled: () => {
                            if (this.enabled.function !== null) {
                                this.enabled.value = this.enabled.function(this.enabled.value);
                            }
                        }
                    };
                }
                setValue(attribute, value, time) {
                    if (attribute.startsWith('{')) {
                        const {
                            id
                        } = JSON.parse(attribute);
                        const {
                            c,
                            i
                        } = this.items[id];
                        if (this.columns[c].items[i].value.function !== null) {
                            value = this.columns[c].items[i].value.function(value);
                        }
                        this.setItem(id, value);
                        return;
                    }
                    super.setValue(attribute, value, time);
                }
                setItem(id, value) {
                    const {
                        c,
                        trace
                    } = this.items[id];
                    const y = Array(this.columns.length);
                    y[c] = value;
                    Plotly.restyle(this.plotly, {
                        y: [y]
                    }, [trace]);
                }
                initData() {
                    this.columns = this.sort(this.columns);
                    this.x = Array(this.columns.length);
                    this.items = {};
                    let trace = 0;
                    for (let c = 0; c < this.columns.length; c++) {
                        this.columns[c].items = this.sort(this.columns[c].items);
                        this.x[c] = this.columns[c].name;
                        const column = this.columns[c];
                        for (let i = 0; i < column.items.length; i++) {
                            const item = column.items[i];
                            const id = item.id;
                            this.items[id] = {
                                id,
                                trace,
                                item,
                                c,
                                i
                            };
                            this.addValueProvider(JSON.stringify({
                                id
                            }), item.value.provider);
                            trace++;
                        }
                    }
                }
                initTraces() {
                    const traces = [];
                    Object.values(this.items).forEach(({
                                                           item
                                                       }) => {
                        const x = this.x;
                        const y = Array(this.columns.length);
                        traces.push({
                            x,
                            y,
                            name: item.name,
                            text: item.displayName ? item.name : '',
                            textposition: item.textposition,
                            type: 'bar'
                        });
                    });
                    return traces;
                }
                initPlotly() {
                    if (this.plotly != null) {
                        return;
                    }
                    this.initData();
                    const d3 = Plotly.d3;
                    const div = d3.select(this.component).style({
                        width: '100%',
                        height: '100%'
                    });
                    this.plotly = div.node();
                    const traces = this.initTraces();
                    const shapes = this.initShapes();
                    const annotations = this.initAnnotations();
                    const images = this.initImages();
                    const layout = {
                        xaxis: this.xaxis,
                        yaxis: this.yaxis,
                        margin: this.margin,
                        legend: this.legend,
                        plot_bgcolor: this.plot_bgcolor,
                        paper_bgcolor: this.paper_bgcolor,
                        shapes,
                        annotations,
                        images,
                        barmode: this.barmode,
                        bargap: this.bargap
                    };
                    const config = {
                        displayModeBar: false,
                        responsive: true
                    };
                    Plotly.newPlot(this.plotly, traces, layout, config);
                    this.initialized = true;
                }
            }
            function initCharts() {
                return new Promise(resolve => {
                    const charts = config.widgets.charts;
                    Object.entries(charts).forEach(([name, configuration]) => {
                        let chart;
                        try {
                            switch (configuration.library) {
                                case 'plotly':
                                    chart = new PlotlyChart(configuration);
                                    break;
                                case 'gamblegram':
                                    chart = new Gamblegram(configuration);
                                    break;
                            }
                        } catch (e) {
                            if (e instanceof ReferenceError) {
                                console.warn(e.message);
                                return;
                            } else {
                                throw e;
                            }
                        }
                        widgets[chart.id] = chart;
                    });
                    resolve();
                });
            }

            ;

            ;

            config.widgets.labels = {

            }
            class Label extends Widget {
                constructor(configuration) {
                    super(configuration, 'label');
                    this.component.htmlFor = this.for;
                    this.handleOnClick = this.handleOnClick.bind(this);
                    this.triggerClick = this.triggerClick.bind(this);
                    this.component.addEventListener('click', this.triggerClick);
                }
                triggerClick() {
                    this.dispatchEvent(new Event('click'));
                }
                handleOnClick() {
                    if (this.target.provider !== null) {
                        this.target.provider.setValue(this.target.reference, this.onClick.value);
                    }
                }
                generateSetters() {
                    this.setters = {
                        enabled: () => {
                            if (this.enabled.function === null) {
                                this.component.disabled = !this.enabled.value;
                                return;
                            }
                            this.component.disabled = !this.enabled.function(this.enabled.value);
                        },
                        visible: () => {
                            if (this.visible.function === null) {
                                this.component.hidden = !this.visible.value;
                                return;
                            }
                            this.component.hidden = !this.visible.function(this.visible.value);
                        },
                        onClick: () => {
                            if (this.onClick.function !== null) {
                                this.onClick.value = this.onClick.function(this.onClick.value);
                            }
                        },
                        label: () => {
                            const label = this.label;
                            let value = label.function ? label.function(label.value) : label.value;
                            if (this.component.innerHTML !== value) {
                                this.component.innerHTML = value;
                            }
                        }
                    };
                }
            }
            function initLabels() {
                return new Promise(resolve => {
                    const labels = config.widgets.labels;
                    Object.entries(labels).forEach(([name, configuration]) => {
                        let label;
                        try {
                            label = new Label(configuration);
                        } catch (e) {
                            if (e instanceof ReferenceError) {
                                console.warn(e.message);
                                return;
                            } else {
                                throw e;
                            }
                        }
                        widgets[label.id] = label;
                    });
                    resolve();
                });
            }

            ;


            config.widgets.ranges = {
                'range_id': {
                    'name': 'e',
                    'target': {
                        'value': null,
                        'provider': '{"type":"MP","id":"e","parent":"model-id"}',
                        'array': !1,
                        'indexes': null,
                        'function': null,
                        'typeof': 'number'
                    },
                    'events': ["change"],
                    'actions': {},
                    'vertical': {
                        'typeof': 'boolean',
                        'value': !1,
                        'complex': !1,
                        'provider': null,
                        'array': !1,
                        'indexes': null,
                        'function': null
                    },
                    'attributes': ["enabled", "min", "max", "reversed"],
                    'enabled': {
                        'typeof': 'boolean',
                        'value': !0,
                        'complex': !1,
                        'provider': null,
                        'array': !1,
                        'indexes': null,
                        'function': null
                    },
                    'reversed': {
                        'typeof': 'boolean',
                        'value': !1,
                        'complex': !1,
                        'provider': null,
                        'array': !1,
                        'indexes': null,
                        'function': null
                    },
                    'min': {'typeof': 'number', 'value': '0', 'complex': !1, 'provider': null, 'array': !1, 'indexes': null, 'function': null},
                    'max': {'typeof': 'number', 'value': '10', 'complex': !1, 'provider': null, 'array': !1, 'indexes': null, 'function': null},
                    'id': 'range_id'
                },
            }



            class Range extends Widget {
                constructor(configuration) {
                    super(configuration, 'range');
                    this.component.step = (this.component.max - this.component.min) / 1000;
                    if (configuration.vertical.value) {
                        this.component.setAttribute('orient', 'vertical');
                        this.component.style['-webkit-appearance'] = 'slider-vertical';
                    }
                    this.handleOnChange = this.handleOnChange.bind(this);
                    this.component.addEventListener('input', this.handleOnChange);
                    this.addValueProvider('target', this.target.provider);
                }
                getValue() {
                    return this.target.value;
                }
                handleOnChange() {
                    let value = this.component.value;
                    if (this.target.function !== null) {
                        value = this.target.function(value);
                    }
                    this.target.value = value;
                    if (this.target.provider) {
                        this.target.provider.setValue(this.target.reference, this.target.value);
                    }
                    this.dispatchEvent(new Event('change'));
                }
                generateSetters() {
                    this.setters = {
                        target: () => {
                            if (this.target.function === null) {
                                this.component.value = this.target.value;
                                return;
                            }
                            this.component.value = this.target.function(this.target.value);
                        },
                        enabled: () => {
                            if (this.enabled.function === null) {
                                this.component.disabled = !this.enabled.value;
                                return;
                            }
                            this.component.disabled = !this.enabled.function(this.enabled.value);
                        },
                        min: () => {
                            if (this.min.function === null) {
                                this.component.min = this.min.value;
                                return;
                            }
                            this.component.min = this.min.function(this.min.value);
                        },
                        max: () => {
                            if (this.max.function === null) {
                                this.component.max = this.max.value;
                                return;
                            }
                            this.component.max = this.max.function(this.max.value);
                        },
                        reversed: () => {
                            if (this.reversed.function !== null) {
                                this.reversed.value = this.reversed.function(this.reversed.value);
                            }
                            this.component.style.setProperty('direction', this.reversed.value ? 'rtl' : 'ltr');
                        }
                    };
                }
                setValueProvider(attribute, id, target) {
                    if (attribute === 'target') {
                        target.registerInitialValueListener(this, id, attribute);
                        this.target.reference = target.registerValueSetter(this, id, attribute);
                        this.target.provider = target;
                        return;
                    }
                    super.setValueProvider(attribute, id, target);
                }
            }
            function initRanges() {
                return new Promise(resolve => {
                    const ranges = config.widgets.ranges;
                    Object.entries(ranges).forEach(([name, configuration]) => {
                        let range;
                        try {
                            range = new Range(configuration);
                        } catch (e) {
                            if (e instanceof ReferenceError) {
                                console.warn(e.message);
                                return;
                            } else {
                                throw e;
                            }
                        }
                        widgets[range.id] = range;
                    });
                    resolve();
                });
            }

            ;

            config.widgets.toggles = {

            }
            class Toggle extends Widget {
                constructor(configuration) {
                    super(configuration, 'toggle');
                    this.handleOnChange = this.handleOnChange.bind(this);
                    this.component.addEventListener('change', this.handleOnChange);
                    if (this.target.value != null) {
                        this.component.checked = this.target.value;
                    }
                    this.addValueProvider('target', this.target.provider);
                }
                getValue() {
                    return this.component.checked;
                }
                handleOnChange() {
                    if (this.constructed == null) {
                        return;
                    }
                    if (this.component.checked) {
                        if (this.target.provider !== null) {
                            this.target.provider.setValue(this.target.reference, this.onToggleOn.value);
                        }
                        this.dispatchEvent(new Event('toggleOn'));
                    } else {
                        if (this.target.provider !== null) {
                            this.target.provider.setValue(this.target.reference, this.onToggleOff.value);
                        }
                        this.dispatchEvent(new Event('toggleOff'));
                    }
                    this.dispatchEvent(new Event('change'));
                    this.target.debounce = true;
                }
                generateSetters() {
                    this.setters = {
                        target: () => {
                            if (this.target.debounce === true) {
                                console.log('debounce');
                                this.target.debounce = false;
                                return;
                            }
                            if (this.target.value != this.component.checked) {
                                this.component.checked = this.target.value;
                                this.handleOnChange();
                            }
                        },
                        enabled: () => {
                            if (this.enabled.function === null) {
                                this.component.disabled = !this.enabled.value;
                                return;
                            }
                            this.component.disabled = !this.enabled.function(this.enabled.value);
                        },
                        visible: () => {
                            if (this.visible.function === null) {
                                this.component.hidden = !this.visible.value;
                                return;
                            }
                            this.component.hidden = !this.visible.function(this.visible.value);
                        },
                        onToggleOn: () => {
                            if (this.onToggleOn.function !== null) {
                                this.onToggleOn.value = this.onToggleOn.function(this.onToggleOn.value);
                            }
                        },
                        onToggleOff: () => {
                            if (this.onToggleOff.function !== null) {
                                this.onToggleOff.value = this.onToggleOff.function(this.onToggleOff.value);
                            }
                        }
                    };
                }
                setValueProvider(attribute, id, target) {
                    if (attribute === 'target') {
                        this.target.reference = target.registerValueSetter(this, id, attribute);
                        this.target.provider = target;
                        target.registerValueListener(this, id, attribute);
                        return;
                    }
                    super.setValueProvider(attribute, id, target);
                }
            }
            function initToggles() {
                return new Promise(resolve => {
                    const toggles = config.widgets.toggles;
                    Object.entries(toggles).forEach(([name, configuration]) => {
                        let toggle;
                        try {
                            toggle = new Toggle(configuration);
                        } catch (e) {
                            if (e instanceof ReferenceError) {
                                console.warn(e.message);
                                return;
                            } else {
                                throw e;
                            }
                        }
                        widgets[toggle.id] = toggle;
                    });
                    resolve();
                });
            }

            ;

            config.models = {
                'model-id': {
                    'mode': modelMode,
                    'interval': parseFloat(interval),
                    'stepSize': parseFloat(stepSize),
                    'startTime': parseFloat(dataFromXml.startTime),
                    'stopAtStopTime': true,
                    'stopTime': parseFloat(stopTime),
                    'tolerance': parseFloat(dataFromXml.tolerance),
                    'destroyOnReset': false,
                    'resetInputsOnTerminate': true,
                    'id': 'model-id',
                    'name': dataFromXml.modelName,
                    'originalName': dataFromXml.modelName,
                    'js': void 0,
                    'hash': 'a3b46336fd9bfd7a9ad82a470ab69ae64a3071dca8050de5f360d92db672f522',
                    'guid':dataFromXml.guid,
                    'identifier': dataFromXml.modelName,
                    'modelName': dataFromXml.modelName,
                    parameters,
                    variables,
                    'description': dataFromXml.description,
                    'generationDateAndTime': dataFromXml.generationDateAndTime,
                    'generationTool': dataFromXml.generationTool,
                    'events': [
                        'start',
                        'stop'
                    ],
                    'actions': {},
                    'populated': true,
                    arrays
                }
            }
            const models = {};

            models["model-id"]=function anonymous(){
                <?php include_once 'models/' . $results['id'] .'/' .$results['id'] .'.js'; ?>
                <?php echo "return " . $results['id'] .";" ?>
            }

            function createModelRuntime(Model, config, functions) {
                return new Promise((resolve, reject) => {
                    Model()().ready.then(model => {
                        model.config = config;
                        model.modelIsSetup = false;
                        model.cwrapFunctions = functions.cwrapFunctions.bind(model);
                        model.cwrapFunctions();
                        model.consoleLogger = functions.consoleLogger.bind(model);
                        model.consoleLoggerPtr = model.addFunction(model.consoleLogger, 'viiiiii');
                        model.init = functions.init.bind(model);
                        model.instantiate = functions.instantiate.bind(model);
                        model.setup = functions.setup.bind(model);
                        model.reset = functions.reset.bind(model);
                        model.resetWithInitial = functions.resetWithInitial.bind(model);
                        model.gettersAndSetters = functions.gettersAndSetters.bind(model);
                        model.gettersAndSetters();
                        model.outputValues = new functions.OutputValues(model);
                        model.valueListeners = [];
                        model.initialValueListeners = [];
                        model.initialValues = {};
                        model.valueSetters = [];
                        model.lastInputValues = {};
                        model.registerValueListener = functions.registerValueListener.bind(model);
                        model.registerArrayListener = functions.registerArrayListener.bind(model);
                        model.registerInitialValueListener = functions.registerInitialValueListener.bind(model);
                        model.registerValueSetter = functions.registerValueSetter.bind(model);
                        model.updateInitialValueListeners = functions.updateInitialValueListeners.bind(model);
                        model.setInitialValues = functions.setInitialValues.bind(model);
                        model.setInitialValueByName = functions.setInitialValueByName.bind(model);
                        model.updateValueByName = functions.updateValueByName.bind(model);
                        model.getValueByName = functions.getValueByName.bind(model);
                        model.disableListener = functions.disableListener.bind(model);
                        model.enableListener = functions.enableListener.bind(model);
                        model.getReferenceFromName = functions.getReferenceFromName.bind(model);
                        model.setSpeed = functions.setSpeed.bind(model);
                        model.config.type = WidgetType.MODEL;
                        if (model.config.mode === 'continuous') {
                            model.play = functions.continuous.play.bind(model);
                            model.pause = functions.continuous.pause.bind(model);
                            model.setValue = functions.continuous.setValue.bind(model);
                            model.modelTick = functions.continuous.modelTick.bind(model);
                            model.stageTick = functions.continuous.stageTick.bind(model);
                            model.updateValueListeners = functions.continuous.updateValueListeners.bind(model);
                        }
                        if (model.config.mode === 'oneshot') {
                            model.play = functions.oneshot.play.bind(model);
                            model.pause = functions.oneshot.pause.bind(model);
                            model.setValue = functions.oneshot.setValue.bind(model);
                            model.updateValueListeners = functions.oneshot.updateValueListeners.bind(model);
                        }
                        console.log('Model '.concat(model.config.name, ' ready.'));
                        resolve(model);
                    }).catch(err => {
                        reject(err);
                    });
                });
            }
            const functions = {
                    'cwrapFunctions': function cwrapFunctions() {
                        this.snprintf = this.cwrap('snprintf', 'number', [
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.createFmi2CallbackFunctions = this.cwrap('createFmi2CallbackFunctions', 'number', ['number']);
                        let prefix = this.config.identifier;
                        let separator = '_';
                        if (typeof this['_fmi2GetVersion'] === 'function') {
                            prefix = '';
                            separator = '';
                        }
                        this.fmi2GetVersion = this.cwrap(''.concat(prefix).concat(separator, 'fmi2GetVersion'), 'string');
                        this.fmi2GetTypesPlatform = this.cwrap(''.concat(prefix).concat(separator, 'fmi2GetTypesPlatform'), 'string');
                        this.fmi2Instantiate = this.cwrap(''.concat(prefix).concat(separator, 'fmi2Instantiate'), 'number', [
                            'string',
                            'number',
                            'string',
                            'string',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.fmi2SetupExperiment = this.cwrap(''.concat(prefix).concat(separator, 'fmi2SetupExperiment'), 'number', [
                            'number',
                            'number',
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.fmi2Reset = this.cwrap(''.concat(prefix).concat(separator, 'fmi2Reset'), 'number', ['number']);
                        this.fmi2FreeInstance = this.cwrap(''.concat(prefix).concat(separator, 'fmi2FreeInstance'), 'number', ['number']);
                        this.fmi2EnterInitializationMode = this.cwrap(''.concat(prefix).concat(separator, 'fmi2EnterInitializationMode'), 'number', ['number']);
                        this.fmi2ExitInitializationMode = this.cwrap(''.concat(prefix).concat(separator, 'fmi2ExitInitializationMode'), 'number', ['number']);
                        this.fmi2GetReal = this.cwrap(''.concat(prefix).concat(separator, 'fmi2GetReal'), 'number', [
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.fmi2SetReal = this.cwrap(''.concat(prefix).concat(separator, 'fmi2SetReal'), 'number', [
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.fmi2GetBoolean = this.cwrap(''.concat(prefix).concat(separator, 'fmi2GetBoolean'), 'number', [
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.fmi2SetBoolean = this.cwrap(''.concat(prefix).concat(separator, 'fmi2SetBoolean'), 'number', [
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                        this.fmi2DoStep = this.cwrap(''.concat(prefix).concat(separator, 'fmi2DoStep'), 'number', [
                            'number',
                            'number',
                            'number',
                            'number'
                        ]);
                    },
                    'consoleLogger': function consoleLogger(componentEnvironment, instanceName, status, category, message, other) {
                        const formatMessage = (message, other) => {
                            let ptr = this._malloc(1);
                            let num = this.snprintf(ptr, 0, message, other);
                            this._free(ptr);
                            num++;
                            ptr = this._malloc(num);
                            this.snprintf(ptr, num, message, other);
                            return ptr;
                        };
                        console.log('FMU(' + this.UTF8ToString(instanceName) + ':' + status + ':' + this.UTF8ToString(category) + ') msg: ' + this.UTF8ToString(formatMessage(message, other)));
                        this._free(formatMessage);
                    },
                    'gettersAndSetters': function gettersAndSetters() {
                        this.createBuffer = function(arr) {
                            let size = arr.length * arr.BYTES_PER_ELEMENT;
                            let ptr = this._malloc(size);
                            return {
                                ptr,
                                size
                            };
                        };
                        this.createAndFillBuffer = function(arr) {
                            const buffer = this.createBuffer(arr);
                            this.fillBuffer(buffer, arr);
                            return buffer;
                        };
                        this.freeBuffer = function(buffer) {
                            if (buffer.ptr !== null) {
                                this._free(buffer.ptr);
                            }
                            buffer.ptr = null;
                            buffer.size = null;
                        };
                        this.viewBuffer = function(buffer) {
                            return new Uint8Array(this.HEAPU8.buffer, buffer.ptr, buffer.size);
                        };
                        this.fillBuffer = function(buffer, arr) {
                            const view = this.viewBuffer(buffer);
                            view.set(new Uint8Array(arr.buffer));
                            return buffer;
                        };
                        this.setReal = function(query, value, count) {
                            return this.fmi2SetReal(this.inst, query.byteOffset, count, value.byteOffset);
                        };
                        this.setBoolean = function(query, value, count) {
                            return this.fmi2SetBoolean(this.inst, query.byteOffset, count, value.byteOffset);
                        };
                        this.getReal = function(query, output, count) {
                            return this.fmi2GetReal(this.inst, query.byteOffset, count, output.byteOffset);
                        };
                        this.getBoolean = function(query, output, count) {
                            return this.fmi2GetBoolean(this.inst, query.byteOffset, count, output.byteOffset);
                        };
                        this.flushSetQueues = function() {
                            this.flushRealQueue();
                            this.flushBooleanQueue();
                        };
                        this.setRealQueue = false;
                        this.setBooleanQueue = false;
                        this.flushBooleanQueue = function() {
                            if (this.setBooleanQueue) {
                                const referenceBuffer = this.createAndFillBuffer(new Int32Array(this.setBooleanQueue.references));
                                const references = this.viewBuffer(referenceBuffer);
                                const valueBuffer = this.createAndFillBuffer(new Int32Array(this.setBooleanQueue.values));
                                const values = this.viewBuffer(valueBuffer);
                                this.setBoolean(references, values, this.setBooleanQueue.references.length);
                                this.freeBuffer(referenceBuffer);
                                this.freeBuffer(valueBuffer);
                                this.setBooleanQueue = false;
                            }
                        };
                        this.flushRealQueue = function() {
                            if (this.setRealQueue) {
                                const referenceBuffer = this.createAndFillBuffer(new Int32Array(this.setRealQueue.references));
                                const references = this.viewBuffer(referenceBuffer);
                                const valueBuffer = this.createAndFillBuffer(new Float64Array(this.setRealQueue.values));
                                const values = this.viewBuffer(valueBuffer);
                                this.setReal(references, values, this.setRealQueue.references.length);
                                this.freeBuffer(referenceBuffer);
                                this.freeBuffer(valueBuffer);
                                this.setRealQueue = false;
                            }
                        };
                        this.setSingleReal = function(reference, value) {
                            if (!this.setRealQueue) {
                                this.setRealQueue = {
                                    references: [],
                                    values: []
                                };
                            }
                            this.setRealQueue.references.push(reference);
                            this.setRealQueue.values.push(value);
                        };
                        this.getSingleReal = function(reference) {
                            const queryBuffer = this.createAndFillBuffer(new Int32Array([reference]));
                            const query = this.viewBuffer(queryBuffer);
                            const outputBuffer = this.createBuffer(new Float64Array(1));
                            const output = this.viewBuffer(outputBuffer);
                            this.getReal(query, output, 1);
                            const real = new Float64Array(output.buffer, output.byteOffset, 1);
                            this.freeBuffer(queryBuffer);
                            this.freeBuffer(outputBuffer);
                            return real[0];
                        };
                        this.setSingleBoolean = function(reference, value) {
                            if (!this.setBooleanQueue) {
                                this.setBooleanQueue = {
                                    references: [],
                                    values: []
                                };
                            }
                            this.setBooleanQueue.references.push(reference);
                            this.setBooleanQueue.values.push(value);
                        };
                        this.getSingleBoolean = function(reference) {
                            const queryBuffer = this.createAndFillBuffer(new Int32Array([reference]));
                            const query = this.viewBuffer(queryBuffer);
                            const outputBuffer = this.createBuffer(new Int32Array(1));
                            const output = this.viewBuffer(outputBuffer);
                            this.getBoolean(query, output, 1);
                            const bool = new Int32Array(output.buffer, output.byteOffset, 1);
                            this.freeBuffer(queryBuffer);
                            this.freeBuffer(outputBuffer);
                            return bool[0];
                        };
                    },
                    'init': function init() {
                        this.instantiate();
                        this.setup();
                    },
                    'instantiate': function instantiate() {
                        const fmi2CoSimulation = 1;
                        const fmi2CallbackFunctionsPtr = this.createFmi2CallbackFunctions(this.consoleLoggerPtr);
                        this.inst = this.fmi2Instantiate(this.config.name, fmi2CoSimulation, this.config.guid, '', fmi2CallbackFunctionsPtr, 0, 0);
                    },
                    'setup': function setup() {
                        this.currentStep = this.config.startTime;
                        this.fmi2SetupExperiment(this.inst, 1, this.config.tolerance || 0.000005, this.config.startTime, 0);
                        this.setInitialValues();
                        this.flushSetQueues();
                        this.fmi2EnterInitializationMode(this.inst);
                        this.fmi2ExitInitializationMode(this.inst);
                        this.outputValues.update();
                        this.updateInitialValueListeners();
                        this.modelTickInterval = null;
                        this.modelIsSetup = true;
                    },
                    'reset': function reset(play = true, resetInputValues = false, destroyOnReset = null) {
                        this.pause();
                        if (destroyOnReset != null ? destroyOnReset : this.config.destroyOnReset) {
                            this.fmi2FreeInstance(this.inst);
                            this.instantiate();
                        } else {
                            this.fmi2Reset(this.inst);
                        }
                        if (resetInputValues) {
                            this.lastInputValues = {};
                        }
                        this.setup();
                        if (play) {
                            this.play();
                        }
                    },
                    'resetWithInitial': function resetWithInitial(play = true, resetInputValues = false, destroyOnReset = null) {
                        this.pause();
                        if (destroyOnReset != null ? destroyOnReset : this.config.destroyOnReset) {
                            this.fmi2FreeInstance(this.inst);
                            this.instantiate();
                        } else {
                            this.fmi2Reset(this.inst);
                        }
                        if (resetInputValues) {
                            this.initialValues = {};
                            this.lastInputValues = {};
                        }
                        this.setup();
                        if (play) {
                            this.play();
                        }
                    },
                    'registerValueListener': function registerValueListener(listener, name, attribute, immediate = false) {
                        const reference = this.getReferenceFromName(name);
                        if (reference === null) {
                            console.log(''.concat(this.config.identifier, ': failed to register value listener ').concat(name, ' from ').concat(listener.name, ' to attribute ').concat(attribute));
                            return false;
                        }
                        const index = this.outputValues.watch(reference);
                        this.valueListeners.push({
                            target: listener,
                            attribute,
                            indicies: null,
                            index,
                            enabled: true,
                            immediate
                        });
                        console.log(''.concat(this.config.identifier, ': registered value listener ').concat(name, ' (').concat(reference, ') from ').concat(listener.name, ' as index ').concat(index, ' to attribute ').concat(attribute));
                        return true;
                    },
                    'registerArrayListener': function registerArrayListener(listener, names, attribute, immediate = false) {
                        if (!Array.isArray(names)) {
                            console.warn(''.concat(this.config.identifier, ': failed to register array listener \'').concat(names, '\' (not an array) from ').concat(listener.name, ' to attribute ').concat(attribute));
                            return false;
                        }
                        const indicies = [];
                        let lastname = null;
                        for (const name of names) {
                            const reference = this.getReferenceFromName(name);
                            if (reference === null) {
                                console.warn(''.concat(this.config.identifier, ': failed to register array listener (').concat(name, ' not found) from ').concat(listener.name, ' to attribute ').concat(attribute));
                                return false;
                            }
                            const index = this.outputValues.watch(reference);
                            lastname = name;
                            indicies.push(index);
                        }
                        this.valueListeners.push({
                            target: listener,
                            attribute,
                            indicies,
                            index: null,
                            enabled: true,
                            immediate
                        });
                        console.log(''.concat(this.config.identifier, ': registered array listener (..., ').concat(lastname, ') from ').concat(listener.name, ' to attribute ').concat(attribute));
                        return true;
                    },
                    'registerInitialValueListener': function registerInitialValueListener(listener, name, attribute) {
                        const reference = this.getReferenceFromName(name);
                        if (reference === null) {
                            return false;
                        }
                        this.initialValueListeners.push({
                            target: listener,
                            attribute,
                            reference
                        });
                        console.log(''.concat(this.config.identifier, ': registered initial value listener ').concat(name, ' (').concat(reference, ')'));
                        return true;
                    },
                    'disableListener': function disableListener(listener, attribute) {
                        if (listener == null || attribute == null) {
                            return false;
                        }
                        const getValueListener = (listener, attribute) => {
                            let found = null;
                            this.valueListeners.forEach(valueListener => {
                                if (valueListener.target === listener && valueListener.attribute === attribute) {
                                    found = valueListener;
                                }
                            });
                            return found;
                        };
                        const valueListener = getValueListener(listener, attribute);
                        if (valueListener == null) {
                            console.log(''.concat(this.config.identifier, ': could not disable listener ').concat(attribute, ' from ').concat(listener.name));
                            return false;
                        } else {
                            valueListener.enabled = false;
                            console.log(''.concat(this.config.identifier, ': disabled listener ').concat(attribute, ' from ').concat(listener.name));
                            return true;
                        }
                    },
                    'enableListener': function enableListener(listener, attribute) {
                        if (listener == null || attribute == null) {
                            return false;
                        }
                        const getValueListener = (listener, attribute) => {
                            let found = null;
                            this.valueListeners.forEach(valueListener => {
                                if (valueListener.target === listener && valueListener.attribute === attribute) {
                                    found = valueListener;
                                }
                            });
                            return found;
                        };
                        const valueListener = getValueListener(listener, attribute);
                        if (valueListener == null) {
                            console.log(''.concat(this.config.identifier, ': could not enable listener ').concat(attribute, ' from ').concat(listener.name));
                            return false;
                        } else {
                            valueListener.enabled = true;
                            console.log(''.concat(this.config.identifier, ': enabled listener ').concat(attribute, ' from ').concat(listener.name));
                            return true;
                        }
                    },
                    'updateInitialValueListeners': function updateInitialValueListeners() {
                        this.initialValueListeners.forEach(listener => {
                            listener.target.setValue(listener.attribute, this.getSingleReal(listener.reference));
                        });
                    },
                    'registerValueSetter': function registerValueSetter(setter, name, attribute) {
                        const reference = this.getReferenceFromName(name);
                        if (reference === null) {
                            return null;
                        }
                        this.valueSetters.push({
                            setter,
                            reference,
                            attribute
                        });
                        console.log(''.concat(this.config.identifier, ': registered value setter for ').concat(name, ' (').concat(reference, ')'));
                        return reference;
                    },
                    'getReferenceFromName': function getReferenceFromName(name) {
                        let reference = null;
                        if (this.config.parameters[name] !== undefined) {
                            reference = this.config.parameters[name].reference;
                        } else if (this.config.variables[name] !== undefined) {
                            reference = this.config.variables[name].reference;
                        }
                        return reference;
                    },
                    'setInitialValues': function setInitialValues() {
                        console.warn("initialValues");
                        for (const [reference, value] of Object.entries(this.initialValues)) {

                            console.warn(value);
                            if (this.lastInputValues[reference] === undefined) {
                                this.setSingleReal(reference, value);
                            }
                        }
                        for (const [reference, value] of Object.entries(this.lastInputValues)) {
                            console.warn(value);
                            this.setSingleReal(reference, value);
                        }
                    },
                    'setInitialValueByName': function setInitialValueByName(name, value) {
                        const reference = this.getReferenceFromName(name);
                        if (reference === null) {
                            console.warn('setInitialValueByName could not find '.concat(name));
                            return;
                        }
                        this.initialValues[reference] = value;
                    },
                    'updateValueByName': function updateValueByName(name, modifier) {
                        if (modifier == null) {
                            console.warn('Can not update '.concat(name, ' modifier is null'));
                            return;
                        }
                        let value = this.getValueByName(name);
                        if (value == null) {
                            return;
                        }
                        typeof modifier === 'function' ? value = modifier(value) : value = Number(modifier);
                        if (isNaN(value)) {
                            console.warn('Can not update '.concat(name, ', value is ').concat(value));
                            return;
                        }
                        const reference = this.getReferenceFromName(name);
                        this.setValue(reference, value);
                    },
                    'getValueByName': function getValueByName(name) {
                        if (this.modelIsSetup === false) {
                            return null;
                        }
                        const reference = this.getReferenceFromName(name);
                        if (reference == null) {
                            console.warn('Can not read '.concat(name, ' - name not found.'));
                            return null;
                        }
                        return this.getSingleReal(reference);
                    },
                    'OutputValues': class OutputValues {
                        constructor(model) {
                            this.model = model;
                            this.references = [];
                            this.valueBuffer = null;
                            this.referenceBuffer = null;
                            this.values = null;
                            this.alloc();
                        }
                        watch(reference) {
                            let index = this.references.indexOf(reference);
                            if (index === -1) {
                                this.references.push(reference);
                                index = this.references.indexOf(reference);
                                this.realloc();
                            }
                            return index;
                        }
                        length() {
                            return this.references.length;
                        }
                        alloc() {
                            const model = this.model;
                            this.valueBuffer = model.createBuffer(new Float64Array(this.length()));
                            this.referenceBuffer = model.createAndFillBuffer(new Int32Array(this.references));
                        }
                        realloc() {
                            this.model.freeBuffer(this.valueBuffer);
                            this.model.freeBuffer(this.referenceBuffer);
                            this.values = null;
                            this.alloc();
                        }
                        update() {
                            const model = this.model;
                            const references = model.viewBuffer(this.referenceBuffer);
                            const values = model.viewBuffer(this.valueBuffer);
                            model.fmi2GetReal(model.inst, references.byteOffset, this.length(), values.byteOffset);
                            this.values = new Float64Array(values.buffer, values.byteOffset, this.length());
                        }
                        value(index) {
                            if (this.values == null) {
                                console.warn('OutputValues can not provide '.concat(index, ' right now'));
                                return null;
                            }
                            return this.values[index];
                        }
                    },
                    'setSpeed': function setSpeed(stepSize, interval) {
                        if (stepSize != null) {
                            stepSize = parseFloat(stepSize);
                            if (isFinite(stepSize)) {
                                this.config.stepSize = stepSize;
                            } else {
                                console.warn('stepSize '.concat(stepSize, ' is not finite.'));
                            }
                        }
                        if (interval != null) {
                            interval = parseFloat(interval);
                            if (isFinite(interval)) {
                                this.config.interval = interval;
                            } else {
                                console.warn('Interval '.concat(interval, ' is not finite.'));
                            }
                        }
                        this.pause();
                        this.play();
                    },
                    'continuous': {
                        'play': function play() {
                            if (this.modelTickInterval !== null) {
                                this.pause();
                            }
                            this.modelTickInterval = window.setInterval(this.modelTick, this.config.interval);
                            createjs.Ticker.addEventListener('tick', this.stageTick);
                            this.running = true;
                        },
                        'pause': function pause() {
                            if (this.modelTickInterval === null) {
                                return;
                            }
                            window.clearInterval(this.modelTickInterval);
                            this.modelTickInterval = null;
                            createjs.Ticker.removeEventListener('tick', this.stageTick);
                            this.running = false;
                        },
                        'setValue': function setValue(reference, value) {
                            //zmena pocas behu
                            this.setSingleReal(reference, value);
                            this.lastInputValues[reference] = value;
                            this.valueSetters.forEach(item => {
                                if (item.reference !== reference) {
                                    return;
                                }
                                item.setter.setValue(item.attribute, value, this.currentStep);
                            });
                            console.log('test' + reference, value)
                        },
                        'modelTick': function modelTick() {
                            this.updateValueListeners(true);
                            this.flushSetQueues();
                            const result = this.fmi2DoStep(this.inst, this.currentStep, this.config.stepSize, 1);
                            if (result === 2 || result === 1) {
                                this.reset(true, this.config.resetInputsOnTerminate);
                                return;
                            }
                            this.currentStep = parseFloat(parseFloat(this.currentStep + this.config.stepSize).toPrecision(8));
                            this.outputValues.update();
                            if (this.config.stopAtStopTime === true && this.currentStep > this.config.stopTime) {
                                this.pause();
                            }
                        },
                        'stageTick': function stageTick() {
                            this.updateValueListeners(false);
                        },
                        'updateValueListeners': function updateValueListeners(immediate) {
                            this.valueListeners.forEach(listener => {
                                if (listener.immediate !== immediate) {
                                    return;
                                }
                                if (listener.enabled === false) {
                                    return;
                                }
                                if (listener.index !== null) {
                                    //zapis do listnera
                                    listener.target.setValue(listener.attribute, this.outputValues.value(listener.index), this.currentStep);
                                } else if (listener.indicies !== null) {
                                    listener.target.setArray(listener.attribute, listener.indicies.map(index => this.outputValues.value(index)), this.currentStep);
                                }
                            });
                        }
                    },
                    'oneshot': {
                        'play': function play() {
                            const values = [];
                            const time = [];
                            this.flushSetQueues();
                            const length = this.outputValues.length();
                            for (let i = 0; i < length; i++) {
                                values[i] = [];
                            }
                            while (this.currentStep <= this.config.stopTime) {
                                for (let i = 0; i < length; i++) {
                                    values[i].push(this.outputValues.value(i));
                                }
                                time.push(this.currentStep);
                                this.fmi2DoStep(this.inst, this.currentStep, this.config.stepSize, 1);
                                this.currentStep = parseFloat(parseFloat(this.currentStep + this.config.stepSize).toPrecision(8));
                                this.outputValues.update();
                            }
                            // zapis hodnot do batchu
                            this.batch = {
                                values,
                                time
                            };
                            this.updateValueListeners();
                        },
                        'pause': function pause() {},
                        'setValue': function setValue(reference, value) {
                            this.lastInputValues[reference] = value;
                            this.reset(false);
                            this.play();
                        },
                        'updateValueListeners': function updateValueListeners() {
                            this.valueListeners.forEach(listener => {
                                if (listener.enabled === false) {
                                    return;
                                }
                                if (listener.index !== null) {
                                    //zapis hodnot do listener odkial berie graf
                                    listener.target.setValues(listener.attribute, this.batch.values[listener.index], this.batch.time);
                                } else if (listener.indicies !== null) {
                                    const values = [];
                                    for (i = 0; i < this.batch.time.length; i++) {
                                        values[i] = listener.indicies.map(index => this.batch.values[index][i]);
                                    }
                                    listener.target.setArrays(listener.attribute, values, this.batch.time);
                                }
                            });
                        }
                    }
                }

            ;
            const javascript = {
                    'onBeforeModelRun': []
                }
            ;

            function getModelByID(id) {
                if (models[id]) {
                    return models[id];
                }
                return null;
            }
            function getModelByName(name) {
                let found = null;
                Object.entries(models).forEach(([, model]) => {
                    if (model.config.name === name) {
                        found = model;
                    }
                });
                return found;
            }
            function getWidgetByName(name) {
                let found = null;
                Object.entries(widgets).forEach(([, widget]) => {
                    if (widget.name === name) {
                        if (found !== null) {
                            console.warn('Multiple widgets named '.concat(name, ', returning last'));
                        }
                        found = widget;
                    }
                });
                return found;
            }
            function getAnimateAnimByID(id) {
                if (widgets[id]) {
                    return widgets[id];
                }
                return null;
            }
            function getAnimateTextByID(id) {
                if (widgets[id]) {
                    return widgets[id];
                }
                return null;
            }
            function getButtonByID(id) {
                if (widgets[id]) {
                    return widgets[id];
                }
                return null;
            }
            function getRangeByID(id) {
                if (widgets[id]) {
                    return widgets[id];
                }
                return null;
            }
            function getToggleByID(id) {
                if (widgets[id]) {
                    return widgets[id];
                }
                return null;
            }
            function getChartByID(id) {
                if (widgets[id]) {
                    return widgets[id];
                }
                return null;
            }

            ;
            const animateFps = 24
            ;
            function initAnimates() {
                const promises = [];
                Object.entries(animates).forEach(([id, {
                    source,
                    root
                }]) => {
                    const element = document.getElementById(id);
                    if (element === null) {
                        return;
                    }
                    const promise = new Promise(resolve => {
                        createAnimateRuntime(root, source, element, id).then(runtime => {
                            animates[id] = runtime;
                            resolve();
                        });
                    });
                    promises.push(promise);
                });
                return promises;
            }
            ;
            function resolveValueProviders() {
                const resolve = id => {
                    const provider = JSON.parse(id);
                    if (provider.type === ProviderType.MODEL_PARAMETER || provider.type === ProviderType.MODEL_VARIABLE) {
                        const target = models[provider.parent];
                        if (target === null) {
                            return null;
                        }
                        const id = provider.id;
                        return {
                            target,
                            id
                        };
                    }
                };
                Object.entries(widgets).forEach(([id, widget]) => {
                    const providers = widget.getValueProviders();
                    const resolved = [];
                    Object.entries(providers).forEach(([attribute, id]) => {
                        resolved[attribute] = resolve(id);
                    });
                    widget.setValueProviders(resolved);
                });
            }
            ;
            function initWidgets() {
                const promises = [];
                promises.push(initAnimateAnimsControlled());
                promises.push(initAnimateAnimsContinuous());
                promises.push(initAnimateTexts());
                promises.push(initAnimatePlays());
                promises.push(initLabels());
                promises.push(initRanges());
                promises.push(initButtons());
                promises.push(initToggles());
                promises.push(initCharts());
                return promises;
            }
            ;
            function initValueProviders() {
                const promises = [];
                Object.entries(models).forEach(([name, model]) => {
                    const configuration = config.models[name];
                    promises.push(new Promise(resolve => {
                        createModelRuntime(model, configuration, functions).then(instance => {
                            models[name] = instance;
                            resolve();
                        });
                    }));
                });
                return promises;
            }
            ;

            function init() {
                createjs.Ticker.framerate = animateFps;
                Promise.all([
                    Promise.all(initValueProviders()),
                    Promise.all(initAnimates())
                ]).then(() => {
                    Promise.all([initWidgets()]).then(() => {
                        resolveValueProviders();

                        Object.entries(models).forEach(([, model]) => model.init());
                        javascript.onBeforeModelRun.forEach(fn => fn());
                        Object.entries(widgets).forEach(([, widget]) => widget.updateComponent());
                    });
                });
            }
            init()

            ;

        }
        document.addEventListener('DOMContentLoaded', mainJS())
    });

</script>

</body>
</html>

