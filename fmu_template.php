<?php

$results['id'] = 'BouncingBall2';
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
        <div id='spinner-blur' class="mt-3">
            <div class="flexblocks-item">
                <div id="chart-id"></div>
            </div>
        </div>
    </div>
</main>

<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/createjs.min.js"></script>
<script src="js/plotly-latest.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootbox.all.min.js"></script>

<script src="FMU/Widget.js"></script>
<script src="FMU/AnimateRuntime.js"></script>
<script src="FMU/AnimateAnimContinuous.js"></script>
<script src="FMU/AnimateAnimControlled.js"></script>
<script src="FMU/AnimateText.js"></script>
<script src="FMU/Button.js"></script>
<script src="FMU/PlotlyBase.js"></script>
<script src="FMU/PlotlyChart.js"></script>
<script src="FMU/Gamblegram.js"></script>
<script src="FMU/Label.js"></script>
<script src="FMU/Range.js"></script>
<script src="FMU/fmu_helpers.js"></script>
<script>

    var dataFromXml = [];
    let id = <?php echo json_encode($results['id']) ?>;
    //let retVal = <?php //echo json_encode($retVal) ?>//;
    let dataSets = <?php echo json_encode($results['dataSets']) ?>;

    let stopTime = <?php echo json_encode($results['stopTime']) ?>;
    let modelMode = <?php echo json_encode($results['modelMode']) ?>;
    let stepSize = <?php echo json_encode($results['stepSize']) ?>;
    let interval = <?php echo json_encode($results['interval']) ?>;

    $(document).ready(function () {
        dataFromXml = parseModelDescription("models/" + id + "/modelDescription.xml", false);

        var htmlVars = "";
        var htmlParams = "";
        var parameters = {};
        var variables = {};
        var arrays = {};
        var datasets = {};
        $.each(dataFromXml.variables, function (i, item) {
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
        console.log(variables)
        $.each(dataFromXml.parameters, function (i, item) {
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
        console.log(parameters)
        $.each(dataFromXml.arrays, function (i, item) {
            arrays[item.cname] = {
                cname: item.cname,
                providers: item.providers
            }
        });
        $.each(dataSets, function (i, item) {
            datasets['dataset-' + i] = {
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
                    color: '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6),
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
                    provider: '{"type":"MV","id":"' + item + '","parent":"model-id"}',
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
        console.log(datasets)

        const mainJS = () => {
            const widgets = []
            const config = {widgets: {}}
            const WidgetType = {
                'MODEL': 'MODEL',
                'CHART': 'CHART',
                'JAVASCRIPT': 'JAVASCRIPT'
            }
            const ProviderType = {
                'MODEL_PARAMETER': 'MP',
                'MODEL_VARIABLE': 'MV'
            }

            config.actions = {
                'pause-action-id': function (model) {
                    model.pause();
                },
                'play-action-id': function (model) {
                    model.play();
                },
                'settimeout-action-id': function () {
                    window.setTimeout(this.updateComponent(), 50);
                },
                'initial-reset-action-id': function (model) {
                    model.resetWithInitial(true, true);
                },
                'new-values-reset-action-id': function (model) {
                    $('input.init-value').each(function () {
                        model.setInitialValueByName($(this).attr('id'), parseFloat($(this).val()));
                    });
                    model.reset(true, true);
                },
                'graf-clear-action-id': function (graf) {
                    graf.clear();
                }
            }


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
                    'guid': dataFromXml.guid,
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

            models["model-id"] = function anonymous() {
                <?php include_once 'models/' . $results['id'] . '/' . $results['id'] . '.js'; ?>
                <?php echo "return " . $results['id'] . ";" ?>
            }

            function createModelRuntime(Model, config, functions) {
                return new Promise((resolve, reject) => {
                    console.log(Model());
                    console.log(Model()());
                    Model()().then(model => {
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
                    this.createBuffer = function (arr) {
                        let size = arr.length * arr.BYTES_PER_ELEMENT;
                        let ptr = this._malloc(size);
                        return {
                            ptr,
                            size
                        };
                    };
                    this.createAndFillBuffer = function (arr) {
                        const buffer = this.createBuffer(arr);
                        this.fillBuffer(buffer, arr);
                        return buffer;
                    };
                    this.freeBuffer = function (buffer) {
                        if (buffer.ptr !== null) {
                            this._free(buffer.ptr);
                        }
                        buffer.ptr = null;
                        buffer.size = null;
                    };
                    this.viewBuffer = function (buffer) {
                        return new Uint8Array(this.HEAPU8.buffer, buffer.ptr, buffer.size);
                    };
                    this.fillBuffer = function (buffer, arr) {
                        const view = this.viewBuffer(buffer);
                        view.set(new Uint8Array(arr.buffer));
                        return buffer;
                    };
                    this.setReal = function (query, value, count) {
                        return this.fmi2SetReal(this.inst, query.byteOffset, count, value.byteOffset);
                    };
                    this.setBoolean = function (query, value, count) {
                        return this.fmi2SetBoolean(this.inst, query.byteOffset, count, value.byteOffset);
                    };
                    this.getReal = function (query, output, count) {
                        return this.fmi2GetReal(this.inst, query.byteOffset, count, output.byteOffset);
                    };
                    this.getBoolean = function (query, output, count) {
                        return this.fmi2GetBoolean(this.inst, query.byteOffset, count, output.byteOffset);
                    };
                    this.flushSetQueues = function () {
                        this.flushRealQueue();
                        this.flushBooleanQueue();
                    };
                    this.setRealQueue = false;
                    this.setBooleanQueue = false;
                    this.flushBooleanQueue = function () {
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
                    this.flushRealQueue = function () {
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
                    this.setSingleReal = function (reference, value) {
                        if (!this.setRealQueue) {
                            this.setRealQueue = {
                                references: [],
                                values: []
                            };
                        }
                        this.setRealQueue.references.push(reference);
                        this.setRealQueue.values.push(value);
                    };
                    this.getSingleReal = function (reference) {
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
                    this.setSingleBoolean = function (reference, value) {
                        if (!this.setBooleanQueue) {
                            this.setBooleanQueue = {
                                references: [],
                                values: []
                            };
                        }
                        this.setBooleanQueue.references.push(reference);
                        this.setBooleanQueue.values.push(value);
                    };
                    this.getSingleBoolean = function (reference) {
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
                        console.log(reference, value)
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
                            console.log(this.outputValues.value(listener.index))
                            if (listener.immediate !== immediate) {
                                return;
                            }
                            if (listener.enabled === false) {
                                return;
                            }
                            if (listener.index !== null) {
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
                    'pause': function pause() {
                    },
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

            const javascript = {
                'onBeforeModelRun': []
            }

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

            const animateFps = 24

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
                    console.log(widget)
                    const providers = widget.getValueProviders();
                    const resolved = [];
                    Object.entries(providers).forEach(([attribute, id]) => {
                        resolved[attribute] = resolve(id);
                    });
                    widget.setValueProviders(resolved);
                });
            }

            function initWidgets() {
                const promises = [];
                promises.push(initCharts());
                console.log(promises)
                return promises;
            }

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

            function init() {
                // createjs.Ticker.framerate = animateFps;
                Promise.all([
                    Promise.all(initValueProviders()),
                ]).then(() => {
                    Promise.all([initWidgets()]).then(() => {
                        resolveValueProviders();

                        Object.entries(models).forEach(([, model]) => model.init());
                        javascript.onBeforeModelRun.forEach(fn => fn());
                        // Object.entries(widgets).forEach(([, widget]) => widget.updateComponent());

                        if (models['model-id'] && models['model-id'].play) {
                            models['model-id'].play(); // Automatically trigger play
                        }
                    });
                });
            }

            init()
        }

        document.addEventListener('DOMContentLoaded', mainJS())
    });

</script>



</body>
</html>
